@extends('emails.layout')

@section('content')
<h2>{{ __('Reset Your Password') }}</h2>
<p>{{ __('Hello') }} {{ $userName }},</p>
<p>{{ __('You requested a password reset for your Interdiscount account. Click the button below to set a new password.') }}</p>
<p style="text-align: center;">
    <a href="{{ $resetUrl }}" class="btn">{{ __('Reset Password') }}</a>
</p>
<p>{{ __('This link will expire in 1 hour.') }}</p>
<p>{{ __('If you did not request a password reset, please ignore this email.') }}</p>
@endsection
