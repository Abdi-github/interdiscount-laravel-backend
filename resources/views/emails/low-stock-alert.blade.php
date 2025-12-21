@extends('emails.layout')

@section('content')
<h2>{{ __('Low Stock Alert') }}</h2>
<p>{{ __('The following product has fallen below the minimum stock level at a store.') }}</p>

<table class="info-table">
    <tr>
        <td><strong>{{ __('Product') }}</strong></td>
        <td>{{ $productName }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('Product Code') }}</strong></td>
        <td>{{ $productCode }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('Store') }}</strong></td>
        <td>{{ $storeName }}</td>
    </tr>
    <tr>
        <td><strong>{{ __('Current Stock') }}</strong></td>
        <td><span class="status-badge">{{ $currentQuantity }}</span></td>
    </tr>
    <tr>
        <td><strong>{{ __('Minimum Stock') }}</strong></td>
        <td>{{ $minStock }}</td>
    </tr>
</table>

<p>{{ __('Please arrange restocking as soon as possible.') }}</p>
@endsection
