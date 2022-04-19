@extends('partials.layout')
@section('title', 'Forgot Password')

@section('content')
    <h1 class="text-3xl font-bold underline">
        Hi {{ $user->name }},
    </h1>

    <p>In order to reset your password, click on the following:
        <a href="{{ config('app.frontend_url') . '?token=' . $token }}">link</a>
    </p>
@endsection
