<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CartService;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CartService $cartService)
    {
        return Inertia::render('Cart/Index', [
            'cartItems' => $cartService->getCartItemsGrouped(),

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CartService $cartService, Product $product)
    {
        $request->mergeIfMissing([
            'quantity' => 1
        ]);
        $data = $request->validate([
            'option_ids' => ['nullable', 'array'],
            'quantity' => ['required', 'integer', 'min:1']
        ]);
        $cartService->addItemToCart(
            $product,
            $data['option_ids'] ?: [],
            $data['quantity']
        );
        return back()->with('success', 'Product added to cart successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Product $product, Request $request, CartService $cartService)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1']
        ]);
        $optionIds = $request->input('option_ids');
        $quantity = $request->input('quantity');
        $cartService->updateItemQuantity($product,  $quantity, $optionIds);
        return back()->with('success', 'Quantity was updated ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CartService $cartService, Product $product)
    {
        $optionIds = $request->input('option_ids');
        $cartService->removeItemFromCart($product, $optionIds);
        return back()->with('success', 'Product was remove from the cart');
    }
    public function checkout(Request $request, CartService $cartService)
    {
        Stripe::setApiKey(config('app.stripe_secret_key'));
        $vendorId = $request->input('vendor_id');

        $allCartItems = $cartService->getCartItemsGrouped();
        DB::beginTransaction();
        try {
            $checkoutCartItems = $allCartItems;

            if ($vendorId) {
                $checkoutCartItems = [$allCartItems[$vendorId]];
            }
            $orders = [];
            $lineItems = [];
            foreach ($checkoutCartItems as $item) {
                $user = $item['user'];
                $cartItems = $item['items'];
                $order = Order::create([
                    'stripe_session_id' => null,
                    'user_id' => $request->user()->id,
                    'vendor_user_id' => $user['id'],
                    'total_price' => $item['totalPrice'],
                    'status' => OrderStatusEnum::Draft->value,

                ]);
                $orders[] = $order;
                foreach ($cartItems as $cartItem) {
                    OrderItem::create([
                        'order_id' => $order['id'],
                        'product_id' => $cartItem['product_id'],
                        'quantity' => $cartItem['quantity'],
                        'price' => $cartItem['price'],
                        'variation_type_option_ids' => $cartItem['option_ids']
                    ]);
                    $description = collect($cartItem['option'])->map(function ($item) {
                        return "{$item['type']['name']} :{$item['name']} ";
                    });
                    $lineItem = [
                        'price_data' => [
                            'currency' => config('app.currency'),
                            'product_data' => [
                                'name' => $cartItem['title'],
                                'images' => [$cartItem['image']],
                            ],
                            'unit_amount' => $cartItem['price'] * 100,
                        ],
                        'quantity' => $cartItem['quantity']
                    ];
                    $lineItems[] = $lineItem;
                }
            }
            $session = Session::create([
                'customer_email' => $request->user()->email,
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.success', []) . "?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => route('stripe.failure', []),
            ]);
            foreach ($orders as $order) {
                $order->stripe_session_id = $session->id;
                $order->save();
            }
            DB::commit();
            return redirect($session->url);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return back()->with('error', $e->getMessage() ?: 'Something Went wrong');
        }
    }
}
