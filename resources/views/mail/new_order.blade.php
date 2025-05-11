{{-- $order coming from public constructor OrderCreaedMail --}}
<x-mail::message>
    <h2 style="text-align: center; font-size: 24px;">
        Congratulations! You have a new Order.
    </h2>
    <x-mail::button :url="$order->id">
        View Order Details
    </x-mail::button>
    <h2 style="font-size: 20px; margin-bottom: 15px;">Order Summary</h2>
    <x-mail::table>
        <table>
            <tbody>
                <tr>
                    <td>#</td>
                    <td>{{$order->id}}</td>
                </tr>
                <tr>
                    <td>Order Date</td>
                    <td>{{$order->created_at}}</td>
                </tr>
                <tr>
                    <td>Order Total</td>
                    <td>{{ \Illuminate\Support\Number::currency($order->total_price)}}</td>
                </tr>
                <tr>
                    <td>Payment Processing Fee</td>
                    <td>{{\Illuminate\Support\Number::currency($order->online_payment_commission ?: 0)}}</td>
                </tr>
                <tr>
                    <td>Platform Fee</td>
                    <td>{{\Illuminate\Support\Number::currency($order->website_commission ?: 0)}}</td>
                </tr>
                <tr>
                    <td>You Earning</td>
                    <td>{{\Illuminate\Support\Number::currency($order->vendor_sub_total ?: 0)}}</td>
                </tr>
            </tbody>
        </table>
    </x-mail::table>
    <hr />
    <x-mail::table>
        <table>
            <thead>
                <tr>
                    <th>
                        Items
                    </th>
                    <th>
                        Quantity
                    </th>
                    <th>
                        Price
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $item)
                    <tr>
                        <td style="padding:5px">
                            <img style="max-width: 60px"
                                src="{{$item->product->getImageForOptions($item->variation_type_option_ids)}}" />
                        </td>
                        <td style="font-size:15px; padding: 5px;">{{$item->product->quantity}}</td>
                        <td style="font-size:15px; padding: 5px;">{{$item->product->title}}</td>
                        <td style="font-size:15px; padding: 5px;">
                            {{\Illuminate\Support\Number::currency($item->price)}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-mail::table>
    <x-mail::panel>
        Thank you for having business with us
    </x-mail::panel>
    Thanks , <br />

    {{config('app.name')}}
</x-mail::message>