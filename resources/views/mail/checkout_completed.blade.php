<x-mail::message>
    <h1 style="text-align: center; font-size: 24px;">Payment was Completed Successfully</h1>
    @foreach ($orders as $order)
<x-mail::table>
    <table>
        <tbody>
            <tr>
                <td>Seller</td>
                <td>
                    <a :href="url('/')">{{$order->vendorUser->vendor->store_name}}</a>
                </td>
            </tr>
            <tr>
                <td>Order #</td>
                <td>#{{$order->id}}</td>
            </tr>
            <tr>
                <td>Items</td>
                <td>{{$order->orderItems->count()}}</td>
            </tr>
            <tr>
                <td>Total</td>
                <td>{{\Illuminate\Support\Number::currency($order->total_price)}}</td>
            </tr>
        </tbody>
    </table>
</x-mail::table>
<x-mail::table>
    <table>
        <thead>
            <tr>
                <th>Items</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
        @foreach($order->orderItems as $item)
            <tr>
                <td style="padding: 5px">
                    <img style="max-width: 60px"
                        src="{{$item->product->getImageForOptions($item->variation_type_option_ids)}}" />
                </td>
                <td style="padding: 5px; font-size: 13px;">
                    {{$item->product->title}}
                </td>
                <td style="padding: 5px; font-size: 13px;">
                    {{$item->quantity}}
                </td>
                <td style="padding: 5px;font-size:13px;">{{\Illuminate\Support\Number::currency($item->price)}}
                </td>
            </tr>
        @endforeach
    </tbody>
    </table>
</x-mail::table>
<x-mail::button :url="$order->id">
    View Order Details
</x-mail::button>
    @endforeach
    Thanks , <br />
    {{config('app.name')}}
</x-mail::message>