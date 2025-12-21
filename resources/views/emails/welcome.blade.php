@extends('emails.layout')

@section('content')
<h2>{{ __('Welcome to Interdiscount!') }}</h2>
<p>{{ __('Hello') }} {{ $userName }},</p>
<p>{{ __('Your email has been verified successfully. You can now enjoy all features of Interdiscount.') }}</p>
<p>{{ __('Start exploring our wide range of electronics and find the best deals.') }}</p>
<p style="text-align: center;">
    <a href="{{ $shopUrl }}" class="btn">{{ __('Start Shopping') }}</a>
</p>
@endsection
