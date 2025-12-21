@extends('emails.layout')

@section('content')
<h2>{{ __('Order Status Update') }}</h2>
<p>{{ __('Hello') }} {{ $userName }},</p>
<p>{{ __('Your order status has been updated.') }}</p>

<table class="info-table">
    <tr>
        <td><strong>{{ __('Order Number') }}</strong></td>
        <td>{{ $orderNumber }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('Status') }}</strong></td>
        <td><span class="status-badge">{{ $status }}</span></td>
    </tr>
</table>

@if($status === 'shipped')
<p>{{ __('Your order has been shipped and is on its way!') }}</p>
@elseif($status === 'delivered')
<p>{{ __('Your order has been delivered. We hope you enjoy your purchase!') }}</p>
@elseif($status === 'cancelled')
<p>{{ __('Your order has been cancelled.') }}</p>
@if(!empty($cancellationReason))
<p><strong>{{ __('Reason') }}:</strong> {{ $cancellationReason }}</p>
@endif
@elseif($status === 'returned')
<p>{{ __('Your return request has been processed.') }}</p>
@elseif($status === 'ready_for_pickup')
<p>{{ __('Your order is ready for pickup at the store.') }}</p>
@endif
@endsection
