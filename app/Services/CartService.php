<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\VariationType;
use App\Models\VariationTypeOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CartService
{
    private ?array $cacheCartItems = null;
    protected const COOKIE_NAME = 'cartItems';
    protected const COOKIE_LIFETIME = 60 * 24 * 365; //1year

    public function addItemToCart(Product $product, $optionIds = null, int $quantity)
    {
        if ($optionIds !== null) {
            $optionIds = $product->variationTypes
                ->mapWithKeys(fn(VariationType $type) => [$type->id => $type->options[0]?->id])->toArray();
        }
        $price = $product->getPriceForOptions($optionIds);
        if (Auth::check()) {
            $this->saveItemToDatabase($product->id, $quantity, $price, $optionIds);
        } else {
            $this->saveItemToCookies($product->id, $quantity, $price, $optionIds);
        }
    }

    public function updateItemQuantity(Product $product, int $quantity, $option_ids = null)
    {
        if (Auth::check()) {
            $this->updateItemQuantityInDatabase($product->id, $quantity, $option_ids);
        } else {
            $this->updateItemQuantityInCookies($product->id, $quantity, $option_ids);
        }
    }

    public function removeItemFromCart(Product $product, $option_ids = null)
    {
        if (Auth::check()) {
            $this->removeItemFromDatabase($product->id, $option_ids);
        } else {
            $this->removeItemFromCookie($product->id, $option_ids);
        }
    }

    public function getCartItems(): array
    {
        try {
            if ($this->cacheCartItems === null) {
                //If the user is authenticated ,retrieve from the database
                if (Auth::check()) {
                    //if the user is authenticated get cart ite from the database
                    $cartItems = $this->getCartItemsFromDatabase();
                    //dd($cartItems);
                } else {
                    //if the user is guest get cart item from the cookie
                    $cartItems = $this->getCartItemsFromCookies();
                    //dd($cartItems);
                }
                $productIds = collect($cartItems)->map(
                    fn($item) => $item['product_id']
                );
                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->with('user.vendor')
                    ->forWebsite()
                    ->get()
                    ->keyBy('id');
                $cartItemData = [];
                foreach ($cartItems as $key => $cartItem) {
                    $product = data_get($products, $cartItem['product_id']);
                    if (!$product) continue;
                    $optionInfo = [];
                    $options = VariationTypeOption::with('variationType')
                        ->whereIn('id', $cartItem['option_ids'])
                        ->get()
                        ->keyBy('id');
                    $imageUrl = null;
                    foreach ($cartItem['option_ids'] as $option_id) {
                        $option = data_get($options, $option_id);
                        if (!$imageUrl) {
                            $imageUrl = $option->getFirstMediaUrl('images', 'small');
                        }
                        $optionInfo[] = [
                            'id' => $option_id,
                            'name' => $option->name,
                            'type' => [
                                'id' => $option->variationType->id,
                                'name' => $option->variationType->name,
                            ]
                        ];
                    }

                    $cartItemData[] = [
                        'id' => $cartItem['id'],
                        'product_id' => $product->id,
                        'title' => $product->name,
                        'slug' => $product->slug,
                        'price' => $cartItem['price'],
                        'quantity' => $cartItem['quantity'],
                        'option_ids' => $cartItem['option_ids'],
                        'option' => $optionInfo,
                        'image' => $imageUrl ?: $product->getFirstMediaUrl('images', 'small'),
                        'user' => [
                            'id' => $product->created_by,
                            'name' => $product->user->vendor->store_name,
                        ]
                    ];
                    // dd($cartItemData);
                }

                $this->cacheCartItems = $cartItemData;
            }
            return $this->cacheCartItems;
        } catch (\Exception $e) {
            throw $e;
            Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
        return [];
    }

    public function getTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->getCartItems() as $cartItem) {
            $totalQuantity += $cartItem['quantity'];
        }
        return $totalQuantity;
    }

    public function getTotalPrice(): float
    {
        $total = 0;
        foreach ($this->getCartItems() as $cartItem) {
            $total += $cartItem['price'] * $cartItem['quantity'];
        }
        return $total;
    }

    public function updateItemQuantityInDatabase(int $productId, int $quantity, array $optionIds = null)
    {
        $userId = Auth::id();
        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('variation_type_option_ids', json_encode($optionIds))
            ->first();
        if ($cartItem) {
            $cartItem->update(['quantity' => $quantity]);
        }
    }

    public function updateItemQuantityInCookies(int $productId, int $quantity, array $optionIds)
    {
        $cartItem = $this->getCartItemsFromCookies();
        ksort($cartItem);
        //use the unique base on product id and option ids
        $itemKey = $productId . '_' . json_encode($optionIds);
        if (isset($cartItem[$itemKey])) {
            $cartItem[$itemKey]['quantity'] = $quantity;
        }
        //save updated cart item back to the cookie
        Cookie::queue(self::COOKIE_NAME, json_encode($cartItem), self::COOKIE_LIFETIME);
    }

    public function saveItemToDatabase(int $productId, int $quantity, $price, array $optionIds)
    {
        $userId = Auth::id();
        ksort($optionIds);
        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('variation_type_option_ids', json_encode($optionIds))
            ->first();
        if ($cartItem) {
            $cartItem->update([
                'quantity' => DB::raw('quantity + ' . $quantity)
            ]);
        } else {
            CartItem::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'variation_type_option_ids' => json_encode($optionIds)
            ]);
        }
    }

    public function saveItemToCookies(int $productId, int $quantity, $price, array $optionIds)
    {
        $cartItems = $this->getCartItemsFromCookies();
        ksort($optionIds);
        //use a unique key base on product id and options id
        $itemkey = $productId . '_' . json_encode($optionIds);
        if (isset($cartItems[$itemkey])) {
            $cartItems[$itemkey]['quantity'] += $quantity;
            $cartItems[$itemkey]['price'] = $price;
        } else {

            $cartItems[$itemkey] = [
                'id' => Str::uuid(),
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'option_ids' => $optionIds
            ];
        }

        //save back update item to the cookie
        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFETIME);
    }

    public function removeItemFromDatabase(int $productId, array $optionIds)
    {
        $userId = Auth::id();
        ksort($optionIds);
        CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('variation_type_option_ids', json_encode($optionIds))
            ->delete();
    }

    public function removeItemFromCookie(int $productId, array $optionIds)
    {
        $cartItems = $this->getCartItemsFromCookies();
        ksort($optionIds);
        //define the cart key
        $cartKey = $productId . '_' . json_encode($optionIds);
        //remove the item from the cart
        unset($cartItems[$cartKey]);
        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFETIME);
    }

    public function getCartItemsFromDatabase()
    {
        $userId = Auth::id();
        $cartItems = CartItem::query()->where('user_id', $userId)
            ->get()
            ->map(function ($cartItem) {

                return [
                    'id' => $cartItem->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'option_ids' => $cartItem->variation_type_option_id ?? [],
                ];
            })->toArray();
        return $cartItems;
    }

    public function getCartItemsFromCookies()
    {
        $cartItems = json_decode(Cookie::get(self::COOKIE_NAME, '[]'), true);
        //dd($cartItems);
        return $cartItems;
    }

    public function getCartItemsGrouped()
    {
        $cartItems = $this->getCartItems();
        //dd(collect($cartItems)->groupBy(fn($item)));
        return collect($cartItems)
            ->groupBy(fn($item) => $item['user']['id'])
            ->map(fn($items, $userId) => [
                'user' => $items->first()['user'],
                'items' => $items->toArray(),
                'totalQuantity' => $items->sum('quantity'),

                'totalPrice' => $items->sum(fn($item) => $item['price'] * $item['quantity']),
            ])->toArray();
    }
    public function moveCartItemToDatabase($userId): void
    {
        $cartItems = $this->getCartItemsFromCookies();
        foreach ($cartItems as $itemkey => $cartItem) {
            $existingItem = CartItem::where('user_id', $userId)
                ->where('product_id', $cartItem['product_id'])
                ->where('variation_type_option_ids', json_encode($cartItem['option_ids']))
                ->first();
            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $cartItem['quantity'],
                    'price' => $cartItem['price']
                ]);
            } else {
                CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $cartItem['product_id'],
                    'quantity' => $cartItem['quantity'],
                    'price' => $cartItem['price'],
                    'variation_type_option_ids' => $cartItem['option_ids'],
                ]);
            }
        }
        Cookie::queue(self::COOKIE_NAME, '', -1);
        //delet cartitem from cookie
    }
}
