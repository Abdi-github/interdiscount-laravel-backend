@extends('emails.layout')

@section('content')
<h2>{{ __('Password Changed Successfully') }}</h2>
<p>{{ __('Hello') }} {{ $userName }},</p>
<p>{{ __('Your password has been changed successfully.') }}</p>
<p>{{ __('If you did not make this change, please contact our support team immediately.') }}</p>
@endsection
