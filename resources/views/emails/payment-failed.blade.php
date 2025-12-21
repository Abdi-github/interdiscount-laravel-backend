@extends('emails.layout')

@section('content')
<h2>{{ __('Payment Failed') }}</h2>
<p>{{ __('Hello') }} {{ $userName }},</p>
<p>{{ __('Unfortunately, the payment for your order could not be processed.') }}</p>

<table class="info-table">
    <tr>
        <td><strong>{{ __('Order Number') }}</strong></td>
        <td>{{ $orderNumber }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('Amount') }}</strong></td>
        <td>{{ $currency }} {{ number_format($amount, 2) }}</td>
    </tr>
    @if(!empty($failureReason))
    <tr>
        <td><strong>{{ __('Reason') }}</strong></td>
        <td>{{ $failureReason }}</td>
    </tr>
    @endif
</table>

<p>{{ __('Please try again or use a different payment method.') }}</p>
@endsection
