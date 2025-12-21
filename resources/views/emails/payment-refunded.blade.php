@extends('emails.layout')

@section('content')
<h2>{{ __('Payment Refunded') }}</h2>
<p>{{ __('Hello') }} {{ $userName }},</p>
<p>{{ __('A refund has been processed for your order.') }}</p>

<table class="info-table">
    <tr>
        <td><strong>{{ __('Order Number') }}</strong></td>
        <td>{{ $orderNumber }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('Refund Amount') }}</strong></td>
        <td>{{ $currency }} {{ number_format($amount, 2) }}</td>
    </tr>
</table>

<p>{{ __('The refund will appear on your statement within 5-10 business days.') }}</p>
@endsection
