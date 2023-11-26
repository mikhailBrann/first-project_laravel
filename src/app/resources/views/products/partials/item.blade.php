<a href="{{route('products.detail', $product->id)}}" class="products__item">
    <div class="products__item-img">
        <img src="https://mac-rent.ru/wp-content/uploads/sites/2/2022/04/q3QdJv5ZSlk.jpg" alt="">
    </div>
    <span class="products__item-name">{{ $product->name }}</span>
    <div class="products__item-status">
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
</a>
