@props(['white' => false])

<img src="{{ asset($white ? 'images/logo_white.png' : 'images/logo.png') }}" {{ $attributes->merge(['class' => 'h-12 w-auto']) }} alt="{{ config('app.name', 'Smart Finance') }}">