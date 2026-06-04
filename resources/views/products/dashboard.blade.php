<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .stat-card {
            transition: transform 0.3s;
            border-radius: 15px;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-chart-line"></i> Product Dashboard</h2>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>

        <div class="row g-4">
            <!-- Total Products -->
            <div class="col-md-3">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Products</h6>
                                <h2 class="mb-0">{{ $totalProducts }}</h2>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Products -->
            <div class="col-md-3">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Active Products</h6>
                                <h2 class="mb-0">{{ $activeProducts }}</h2>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deleted Products -->
            <div class="col-md-3">
                <div class="card stat-card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Deleted Products</h6>
                                <h2 class="mb-0">{{ $deletedProducts }}</h2>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="col-md-3">
                <div class="card stat-card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Low Stock</h6>
                                <h2 class="mb-0">{{ $lowStockProducts }}</h2>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Products</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>₹{{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->stock_quantity }}</td>
                                <td>{{ $product->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>