@extends('emails.layout')

@section('content')
<h2>{{ __('Order Confirmation') }}</h2>
<p>{{ __('Hello') }} {{ $userName }},</p>
<p>{{ __('Thank you for your order! Your order has been placed successfully.') }}</p>

<table class="info-table">
    <tr>
        <td><strong>{{ __('Order Number') }}</strong></td>
        <td>{{ $orderNumber }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('Date') }}</strong></td>
        <td>{{ $orderDate }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('Payment Method') }}</strong></td>
        <td>{{ $paymentMethod }}</td>
    </tr>
</table>

<h3>{{ __('Order Items') }}</h3>
<table class="info-table">
    <tr>
        <td><strong>{{ __('Product') }}</strong></td>
        <td><strong>{{ __('Qty') }}</strong></td>
        <td><strong>{{ __('Price') }}</strong></td>
    </tr>
    @foreach($items as $item)
    <tr>
        <td>{{ $item['product_name'] }}</td>
        <td>{{ $item['quantity'] }}</td>
        <td>{{ $item['currency'] }} {{ number_format($item['total_price'], 2) }}</td>
    </tr>
    @endforeach
</table>

<table class="info-table">
    <tr>
        <td><strong>{{ __('Subtotal') }}</strong></td>
        <td>{{ $currency }} {{ number_format($subtotal, 2) }}</td>
    </tr>
    @if($discount > 0)
    <tr>
        <td><strong>{{ __('Discount') }}</strong></td>
        <td>-{{ $currency }} {{ number_format($discount, 2) }}</td>
    </tr>
    @endif
    <tr>
        <td><strong>{{ __('Shipping') }}</strong></td>
        <td>{{ $currency }} {{ number_format($shippingFee, 2) }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('Total') }}</strong></td>
        <td><strong>{{ $currency }} {{ number_format($total, 2) }}</strong></td>
    </tr>
</table>
@endsection
