<!DOCTYPE html>
<html>

<head>
    <title>View Product</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-4">
        <div class="card shadow">
            <!-- Card Header -->
            <div class="card-header">
                <h5>Product Details</h5>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <!-- Product Name -->
                <p><strong>Name:</strong> {{ $product->name }}</p>

                <!-- Product Price -->
                <p><strong>Price:</strong> ₹{{ $product->price }}</p>

                <!-- Product Description -->
                <p><strong>Description:</strong> {{ $product->description ?? '-' }}</p>

                <!-- Product Status -->
                <p>
                    <strong>Status:</strong>
                    <span class="badge {{ $product->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                        {{ $product->status == 'active' ? 'Active' : 'Inactive' }}
                    </span>
                </p>

                <!-- Product Created Date -->
                <p><strong>Created At:</strong> {{ $product->created_at->format('d M Y') }}</p>
            </div>

            <!-- Card Footer -->
            <div class="card-footer">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>

</body>

</html>
