<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    
    <style>
        .d-flex {
            display: flex;
        }

        .justify-content-center {
            justify-content: center;
        }

        .align-items-center {
            align-items: center;
        }

        .card {
            width: 18rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card-img {
            width: 100%;
            height: 170px;
            object-fit: cover;
            border: 1px solid #f0f0f0;
        }

        .card-content {
            padding: 15px;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin: 0;
            color: #333;
        }

        .card-description {
            font-size: 0.9rem;
            color: #777;
            margin: 10px 0;
            text-align: justify;
        }

        .card-button {
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .card-button:hover {
            background-color: #0056b3;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <a href="{{ route('order_retrieve') }}">View Orders</a>
                </div>
            </div>
        </div>
    </div>

    @if (request()->has('success'))
        {{ request('success') }}
    @endif

    @if (request()->has('cancel'))
        {{ request('cancel') }}
    @endif

    <div class="d-flex justify-content-center align-items-center">
        @foreach ($products as $product)
            @php
                $product_price = null;
                foreach ($prices as $price) {
                    if ($price->product == $product->id && $price->active) {
                        $product_price = $price;
                        break;
                    }
                }
            @endphp

            @if ($product_price)
                <div class="card">
                    <img src="" alt="prod_img" class="card-img">
                    <div class="card-content">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-description">
                            {{ number_format($product_price->unit_amount / 100, 2) }} {{ strtoupper($product_price->currency) }}
                        </p>
                        <p class="card-description">{{ \Illuminate\Support\Str::words($product->description, 30) }}</p>

                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            @method('POST')

                            <input type="hidden" value="{{ $product_price->id }}" name="price_id">
                            <button type="submit" class="card-button">BUY</button>
                        </form>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    </div>
    
</x-app-layout>
