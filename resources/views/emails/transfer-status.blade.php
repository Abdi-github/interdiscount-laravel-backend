@extends('emails.layout')

@section('content')
<h2>{{ __('Stock Transfer Update') }}</h2>
<p>{{ __('A stock transfer has been updated.') }}</p>

<table class="info-table">
    <tr>
        <td><strong>{{ __('Transfer Number') }}</strong></td>
        <td>{{ $transferNumber }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('From Store') }}</strong></td>
        <td>{{ $fromStore }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('To Store') }}</strong></td>
        <td>{{ $toStore }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('Status') }}</strong></td>
        <td><span class="status-badge">{{ $status }}</span></td>
    </tr>
</table>

@if(!empty($items))
<h3>{{ __('Transfer Items') }}</h3>
<table class="info-table">
    <tr>
        <td><strong>{{ __('Product') }}</strong></td>
        <td><strong>{{ __('Quantity') }}</strong></td>
    </tr>
    @foreach($items as $item)
    <tr>
        <td>{{ $item['product_name'] }}</td>
        <td>{{ $item['quantity'] }}</td>
    </tr>
    @endforeach
</table>
@endif
@endsection
