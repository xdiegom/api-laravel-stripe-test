@extends('partials.layout')
@section('title', 'Forgot Password')

@section('content')
    <h1 class="text-3xl font-bold underline">
        Hi {{ $user->name }},
    </h1>

    <p>Here is your password reset token: <strong>{{ $token }}</strong> </p>
@endsection
