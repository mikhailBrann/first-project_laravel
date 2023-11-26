@extends('layout.main')

@section('title', 'Список товаров')
@section('show_page_title', 'Y')
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
            <div class="products__paginate-wrap">
                {{ $products->links() }}
            </div>
        </div>
    </section>
@endsection
