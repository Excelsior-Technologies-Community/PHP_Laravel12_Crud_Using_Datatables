<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Activity Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h5>Activity Logs: {{ $product->name }}</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($product->logs as $log)
                    <div class="mb-3 pb-3 border-bottom">
                        <strong>{{ ucfirst($log->action) }}</strong>
                        <span class="text-muted float-end">{{ $log->created_at->format('d M Y H:i:s') }}</span>
                        <br>
                        <small>IP: {{ $log->ip_address }}</small>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Back</a>
            </div>
        </div>
    </div>
</body>
</html>