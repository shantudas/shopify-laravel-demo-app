<!-- resources/views/shop.blade.php -->
@extends('layouts.app')

@section('title', 'Shop')

@section('content')

    <div class="container mx-auto py-4">
        <a href="#"
           class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">

            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $shopDomain ?? Auth::user()->name }}</h5>
            <p class="font-normal text-gray-700 dark:text-gray-400">{{ $shopDomain ?? Auth::user()->id }}</p>
        </a>
@endsection


{{--@extends('shopify-app::layouts.default')--}}

{{--@section('content')--}}
{{--    <!-- You are: (shop domain name) -->--}}

{{--    --}}
{{--    <div class="container">--}}
{{--        <p>SHOP NAME: {{ $shopDomain ?? Auth::user()->name }}</p>--}}
{{--        <p>USER ID: {{ $shopDomain ?? Auth::user()->id }}</p>--}}
{{--    </div>--}}
{{--@endsection--}}

{{--@section('scripts')--}}
{{--    @parent--}}

{{--    <script>--}}
{{--        actions.TitleBar.create(app, { title: 'Welcome' });--}}
{{--    </script>--}}
{{--@endsection--}}
