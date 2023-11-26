@extends('admin.partials.adminPanel')
@section('title', 'Продукция')
@section('admin_user', $user)

@section('admin-content')
    <div class="admin__product-list product-list">
        <a href="{{ route("admin.products.create") }}" class="product-list__add-btn btn btn-blue">Добавить</a>
        <div class="product-list__wrap">
            <div class="product-list__top">
                <div class="product-list__top-item">Артикул</div>
                <div class="product-list__top-item">Название</div>
                <div class="product-list__top-item">Статус</div>
                <div class="product-list__top-item">Атрибуты</div>
            </div>
            <div class="product-list__bottom">
                @foreach($products as $product)
                <a href="{{ route("admin.products.show", $product->id) }}" class="product-list__bottom-item">
                    <div class="product-list__bottom-item-props">{{ $product->article }}</div>
                    <div class="product-list__bottom-item-props">{{ $product->name }}</div>
                    <div class="product-list__bottom-item-props">
                        {{ $product->status == 'available' ? 'Доступен' : 'Не доступен' }}
                    </div>
                    <div class="product-list__bottom-item-props props-arr">
                        @foreach($product->data as $data => $value)
                            <div>{{$data}}: {{$value}}</div>
                        @endforeach
                    </div>
                </a>
                @endforeach
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
