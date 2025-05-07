@extends('admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-3">
                    <h6 class="text-dark text-capitalize">Edit Item: {{ $item->name }}</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="{{ route('inventory.items.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Item Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" class="form-control">{{ $item->description }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="brand_id" class="form-label">Brand</label>
                            <select name="brand_id" class="form-control">
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $item->brand_id == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select name="category_id" class="form-control">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ $item->sku }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" value="{{ $item->stock }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="sale_price" class="form-label">Sale Price</label>
                            <input type="text" name="sale_price" class="form-control" value="{{ $item->sale_price }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="purchase_price" class="form-label">Purchase Price</label>
                            <input type="text" name="purchase_price" class="form-control" value="{{ $item->purchase_price }}" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Item</button>
                        <a href="{{ route('inventory.items') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
