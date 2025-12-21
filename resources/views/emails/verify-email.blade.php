@extends('emails.layout')

@section('content')
<h2>{{ __('Verify Your Email Address') }}</h2>
<p>{{ __('Hello') }} {{ $userName }},</p>
<p>{{ __('Thank you for registering with Interdiscount. Please click the button below to verify your email address.') }}</p>
<p style="text-align: center;">
    <a href="{{ $verificationUrl }}" class="btn">{{ __('Verify Email') }}</a>
</p>
<p>{{ __('This link will expire in 24 hours.') }}</p>
<p>{{ __('If you did not create an account, no further action is required.') }}</p>
@endsection
