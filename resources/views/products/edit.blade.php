<!DOCTYPE html>
<html>

<head>
    <title>Edit Product</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-4">
        <div class="card shadow">
            <!-- Card Header -->
            <div class="card-header">
                <h5>Edit Product</h5>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <!-- Edit Product Form -->
                <form method="POST" action="{{ route('products.update', $product->id) }}">
                    @csrf <!-- CSRF token for security -->

                    <!-- Product Name -->
                    <div class="mb-3">
                        <label>Product Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label>Price</label>
                        <input type="number" name="price" class="form-control" value="{{ $product->price }}" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control">{{ $product->description }}</textarea>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <!-- Pre-select the current status -->
                            <option value="active" {{ isset($product) && $product->status == 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ isset($product) && $product->status == 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                    <!-- Submit & Back Buttons -->
                    <button class="btn btn-primary">Update</button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
