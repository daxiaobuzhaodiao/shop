<div class="row">
    @foreach($products as $product)
        <div class="card-deck col-md-3 col-sm-4 mb-3 p-0 m-0">
            <div class="card p-1">
                <a href="{{ route('product.show', $product->id) }}"><img class="card-img-top border" src="{{ $product->image_url }}" alt="Card image cap"></a>
                <div class="col card-body px-3 py-0">
                    <div class="col">
                        <div><b>$</b>{{ $product->price }}</div>
                        <div>{{ $product->title }}</div>
                    </div>
                    <div class="col mx-auto">
                        <span class="float-left">销量:{{ $product->sold_count }}</span>
                        <span class="float-right">评分:{{ $product->rating }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>