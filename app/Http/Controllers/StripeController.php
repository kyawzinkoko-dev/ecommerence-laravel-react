<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Http\Resources\OrderViewResource;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Doctrine\DBAL\Driver\PgSQL\Exception\UnexpectedValue;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as FacadesLog;
use Inertia\Inertia;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeController extends Controller
{
    public function success(Request $request)
    {
        $user = Auth::user();
        $sessionId = $request->get('session_id');
        $orders = Order::query()->where('stripe_session_id', $sessionId)->get();
        if ($orders->count() === 0) {
            abort(404);
        }
        foreach ($orders as $key => $order) {
            if ($order->user_id !== $user->id) {
                abort(403);
            }
        }

        return Inertia::render('Stripe/Success', [
            'orders' => OrderViewResource::collection($orders)
        ]);
    }
    public function failure()
    {
        return Inertia::render('Stripe/Failure');
    }

    public function webhook(Request $request)
    {
        $stripe = new StripeClient(config('app.stripe_secret_key'));
        $endpoint_secret = config('app.stripe_webhook_secret');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;
        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (UnexpectedValue $e) {
            FacadesLog::error($e);
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            FacadesLog::error($e);
            return response('Invalid payload', 400);
        }
        FacadesLog::info("===========================");
        FacadesLog::info("====================================");
        FacadesLog::info($event->type);
        FacadesLog::info($event);

        //Handle the event 
        switch ($event->type) {
            case 'charge.updated':
                $charge = $event->data->object;
                $transactionId = $charge['balance_transaction'];
                $paymentIntend = $charge['payment_intent'];
                $balanceTransaction = $stripe->balanceTransactions->retrieve($transactionId);
                $orders = Order::query()->where('payment_intent', $paymentIntend)->get();
                $totalAmount = $balanceTransaction['amount'];
                $stripeFee = 0;

                foreach ($balanceTransaction['fee_details'] as $fee_detail) {
                    if ($fee_detail['type'] === 'stripe_fee') {
                        $stripeFee = $fee_detail['amount'];
                    }
                }
                $platformFeePercentage = config('app.platform_fee_pct');
                foreach ($orders as $order) {
                    $vendorShare = $order->total_price / $totalAmount;  //
                    /** @var Order $order */
                    $order->online_payment_commission = $vendorShare * $stripeFee;
                    $order->website_commsission = ($order->total_price - $order->online_payment_commission) / 100 * $platformFeePercentage;
                    $order->vendor_sub_total = $order->total_price - $order->online_payment_commission - $order->website_commission;
                    $order->save();
                }
                //send email to buyer

            case 'checkout.session.completed':
                $session = $event->data->object;
                $pi = $session['payment_intent'];
                $orders = Order::query()
                    ->with(['orderItems'])
                    ->where('stripe_session_id', '=', $session['id'])
                    ->get();
                $productToDeleteFromCart = [];
                foreach ($orders as $key => $order) {
                    $order->payment_intent = $pi;
                    $order->status = OrderStatusEnum::Paid->value;
                    $order->save();

                    $productToDeleteFromCart = [
                        ...$productToDeleteFromCart,
                        ...$order->orderItems->map(fn($item) => $item->prduct_id)->toArray()
                    ];

                    //Reduce Product quantity
                    foreach ($order->orderItems as $key => $orderItem) {
                        /** @var OrderItem $orderItem */
                        $options = $orderItem->variation_type_option_ids;
                        $product = $orderItem->product;
                        if ($options) {
                            ksort($options);
                            $variation = $product->variations()
                                ->where('variation_type_option_ids', $options)
                                ->first();
                            if ($variation && $variation->quantity != null) {
                                $variation->quantity -= $orderItem->quantity;
                                $variation->save();
                            } else if ($product->quantity != null) {
                                $product->quantity -= $orderItem->quantity;
                                $product->save();
                            }
                        }
                    }
                }
                CartItem::query()->where('user_id', $order->user_id)
                    ->whereIn('product_id', $productToDeleteFromCart)
                    ->where('saved_for_later', false)
                    ->delete();

            default:
                echo "Receive Unknown Event Type" . $event->type;
        }
        return response('', 200);
    }
}
