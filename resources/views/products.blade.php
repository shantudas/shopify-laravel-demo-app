@extends('shopify-app::layouts.default')
@section('content')
    <!-- You are: (shop domain name) -->
    <p>You are: {{ $shopDomain ?? Auth::user()->name }}</p>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Shopify ID</th>
            <th scope="col">Title</th>
            <th scope="col">Price</th>
            <th scope="col">Added At</th>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $product)
            <tr>
                <th>{{$product->id}}</th>
                <th>{{$product->shopify_id}}</th>
                <td>{{$product->title}}</td>
                <td>{{$product->price}}</td>
                <td>{{$product->created_at}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection

@section('scripts')
    @parent

    <script>
        actions.TitleBar.create(app, {title: 'Welcome'});
    </script>
@endsection
