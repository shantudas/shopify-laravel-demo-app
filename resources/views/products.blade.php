<!-- resources/views/shop.blade.php -->
@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="container mx-auto py-4">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Image
                    </th>
                    <th scope="col" class="px-6 py-3">
                        TITLE
                    </th>
                    <th scope="col" class="px-6 py-3">
                        PRICE
                    </th>
                    <th scope="col" class="px-6 py-3">
                        STATUS
                    </th>
                    <th scope="col" class="px-6 py-3">
                        ACTION
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                        <td class="px-6 py-4">
                            <img src="{{$product->image_src}}" alt="{{$product->title}}" class="w-16 h-16 object-cover">
                        </td>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <p>{{$product->title}}</p>
                        </th>
                        <td class="px-6 py-4">
                            {{$product->price}}
                        </td>
                        <td class="px-6 py-4">
                            @if($product->status === 'ACTIVE')
                                <span class="text-green-600 font-bold">Active</span>
                            @elseif($product->status === 'ARCHIVED')
                                <span class="text-grey-600 font-bold">Archived</span>
                            @else
                                <span class="text-red-600 font-bold">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

