@extends('layout.main')

@section('title', $product->name)
@section('content')
    <section class="product">
        <div class="content">
            <a href="{{route('products.list')}}" class="to-catalog">В каталог</a>
            <div class="product-detail">
                <div class="product-detail__img">
                    <img src="https://mac-rent.ru/wp-content/uploads/sites/2/2022/04/q3QdJv5ZSlk.jpg" alt="">
                </div>
                <div class="product-detail__info">
                    <div class="product-detail__article">Артикул: {{ $product->article }}</div>
                    <h1 class="product-detail__title">{{ $product->name }}</h1>
                    <div class="product-detail__status">
                        Доступность:
                        @if($product->status == 'available')
                            <span class="status__avaliable">
                                {{ $product->status }}
                            </span>
                        @else
                            <span class="status__no-avaliable">
                                {{ $product->status }}
                            </span>
                        @endif
                    </div>

                    <div class="product-detail__data">
                        <span class="product-detail__data-title">Характеристики:</span>
                        <div class="product-detail__data-list">
                            @foreach(json_decode($product->data) as $prop => $val)
                                <div class="product-detail__data-item">
                                    <span>{{ $prop }}:</span>
                                    <span>{{ $val }}</span>
                                </div>

                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
