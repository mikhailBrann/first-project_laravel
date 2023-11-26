@extends('layout.main')

@section('title', 'FP Laravel')
@section('content')
    <section class="products">
        <div class="content">
            <div class="products__list">
                @foreach($products as $product)
                    @include("products.partials.item", [
                        "product" => $product,
                    ])
                @endforeach
            </div>
        </div>
    </section>
@endsection
