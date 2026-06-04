<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; }
        .form-container { max-width: 600px; margin: 50px auto; }
        .card { border-radius: 8px; border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .form-label { font-weight: 500; margin-bottom: 5px; }
        .error-text { color: #dc3545; font-size: 12px; margin-top: 5px; }
    </style>
</head>
<body>

<div class="form-container">
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Add New Product</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('products.store') }}" id="productForm">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price *</label>
                    <input type="number" name="price" step="0.01" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control" value="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Product</button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>