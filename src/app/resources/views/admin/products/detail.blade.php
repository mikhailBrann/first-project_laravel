@extends('admin.partials.adminPanel')
@section('title', $product->name)
@section('admin_user', $user)

@section('admin-content')
    <div class="admin__form">
        <div class="admin__form-wrap">
            <div class="admin__form-detail-top">
                <span class="admin__form-detail-title">{{$product->name}}</span>
                <div class="admin__form-detail-control">
                    <div class="admin__form-detail-btns">
                        <a href="{{ route("admin.products.edit", $product->id) }}"
                           class="admin__form-detail-btn change"></a>
                        <form action="{{ route("admin.products.destroy", $product->id) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="admin__form-detail-btn delete"></button>
                        </form>
                    </div>
                    <a href="{{route('admin.products.index')}}" class="admin__form-detail-close"></a>
                </div>
            </div>
            <div class="admin__form-detail-bottom">
                <div class="products-detail__proplist">
                    <div class="products-detail__propitem">
                        <span class="products-detail__propkey">Артикул</span>
                        <span class="products-detail__propvalue">{{$product->article}}</span>
                    </div>
                    <div class="products-detail__propitem">
                        <span class="products-detail__propkey">Название</span>
                        <span class="products-detail__propvalue">{{$product->name}}</span>
                    </div>
                    <div class="products-detail__propitem">
                        <span class="products-detail__propkey">Статус</span>
                        <span class="products-detail__propvalue">{{$product->status}}</span>
                    </div>
                    <div class="products-detail__propitem">
                        <span class="products-detail__propkey">Атрибуты</span>
                        <span class="products-detail__propvalue">
                            @foreach($product->data as $data => $value)
                                <span>{{$data}}: {{$value}}</span>
                            @endforeach
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
