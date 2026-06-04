<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; }
        .details-container { max-width: 600px; margin: 50px auto; }
        .card { border-radius: 8px; border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .detail-row { padding: 12px 0; border-bottom: 1px solid #eee; }
        .detail-label { font-weight: 600; width: 120px; display: inline-block; }
        .status-active { color: #155724; background: #d4edda; padding: 3px 10px; border-radius: 20px; font-size: 12px; }
        .status-inactive { color: #721c24; background: #f8d7da; padding: 3px 10px; border-radius: 20px; font-size: 12px; }
    </style>
</head>
<body>

<div class="details-container">
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Product Details</h5>
        </div>
        <div class="card-body">
            
            <div class="detail-row">
                <span class="detail-label">Product Name:</span>
                <span>{{ $product->name }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Price:</span>
                <span>${{ number_format($product->price, 2) }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Stock:</span>
                <span>{{ $product->stock_quantity ?? 0 }} units</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span>{{ $product->description ?? '-' }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="status-{{ $product->status }}">
                    {{ ucfirst($product->status) }}
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Created:</span>
                <span>{{ $product->created_at ? $product->created_at->format('d M Y, h:i A') : '-' }}</span>
            </div>

            <div class="mt-4">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to List</a>
                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">Edit Product</a>
            </div>
            
        </div>
    </div>
</div>

</body>
</html>