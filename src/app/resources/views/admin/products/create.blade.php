@extends('admin.partials.adminPanel')
@section('title',  isset($product) ? "Редактировать продукт ID {$product->id}" : 'Добавить продукт')
@section('admin_user', $user)

@section('admin-content')
    <div class="admin__form" >
        <div class="admin__form-wrap">
            <div class="admin__form-detail-top">
                <span class="admin__form-detail-title">{{ isset($product) ? "Редактировать {$product->name}" : 'Добавить продукт' }}</span>
                <div class="admin__form-detail-control">
                    <a href="{{route('admin.products.index')}}" class="admin__form-detail-close"></a>
                </div>
            </div>
            <div class="admin__form-detail-bottom">
                <form method="POST"
                      action="{{ isset($product) ? route("admin.products.update", $product->id) : route("admin.products.store") }}"
                      class="admin__form-change-product form-change-product form">

                    @csrf
                    @if(isset($product))
                        @method('PUT')
                        <input name="id" type="hidden" value="{{$product->id}}">
                    @endif


                    <div class="form-change-product__field">
                        <div class="form-change-product__field-title">Артикул</div>
                        <div class="form-change-product__field-value">
                        @if(($user->admin == true) || (isset($product) == false))
                            <input name="article" type="text"
                                   class="form__input  @error('article') border-red-500 @enderror"
                                   placeholder="артикул"
                                   value="{{ (isset($product) && $product->article) ? trim($product->article) : '' }}" />
                        @else
                            <input name="article" type="hidden"
                                   value="{{ (isset($product) && $product->article) ? $product->article : '' }}" />
                            <div class="form__input pseudo__input">{{ (isset($product) && $product->article) ? trim($product->article) : '' }}</div>
                        @endif
                        @error('article')
                            <p class="text-red-500">{{ $message }}</p>
                        @enderror
                        </div>
                    </div>


                    <div class="form-change-product__field">
                        <div class="form-change-product__field-title">Название</div>
                        <div class="form-change-product__field-value">
                            <input name="name" type="text"
                                   class="form__input @error('name') border-red-500 @enderror"
                                   placeholder="Название"
                                   value="{{ (isset($product) && $product->name) ? trim($product->name) : '' }}" />
                            @error('name')
                            <p class="text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="form-change-product__field">
                        <div class="form-change-product__field-title">Статус</div>
                        <div class="form-change-product__field-value">
                            <div class="form-dropdown">
                                <input name="status" type="hidden"
                                       class="form__input @error('status') border-red-500 @enderror"
                                       placeholder="Статус"
                                       value="{{ (isset($product) && $product->status) ? $product->status : 'unavailable' }}" />
                                <div class="form-dropdown__cont">
                                    <div class="form-dropdown__checker form__input"
                                         data-value="{{ (isset($product) && $product->status) ? $product->status : 'unavailable' }}">
                                        {{ (isset($product) && $product->status == 'available') ? 'Доступен' : 'Не доступен' }}
                                    </div>
                                    <div class="form-dropdown__list">
                                        <div class="form-dropdown__item form__input"
                                             data-type-prop="status"
                                             data-value="available"
                                             data-name="Доступен">
                                            Доступен
                                        </div>
                                        <div class="form-dropdown__item form__input"
                                             data-type-prop="status"
                                             data-value="unavailable"
                                             data-name="Не доступен">
                                            Не доступен
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('status')
                            <p class="text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="form-change-product__field">
                        <div class="form-change-product__field-title big">Атрибуты</div>
                        <div class="form-change-product__field-value-arr">
                            <div class="form-todolist">
                                <input name="data" type="hidden"
                                   class="form__input @error('data') border-red-500 @enderror"
                                   placeholder="Название" value="{{ (isset($product) && $product->data) ? $product->data : '{}' }}" />
                                <div class="form-todolist__cont">
                                    <div class="form-todolist__list"></div>
                                    <div class="form-todolist__added-btn">+ Добавить атрибут</div>
                                </div>
                            </div>
                            @error('data')
                            <p class="text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="form__submit btn btn-blue">Сохранить</button>
                </form>
            </div>
        </div>
    </div>
@endsection
