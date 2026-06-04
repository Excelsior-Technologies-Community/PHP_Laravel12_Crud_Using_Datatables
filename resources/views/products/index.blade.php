<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Simple CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    
    <style>
        body { background: #f5f5f5; }
        .container { max-width: 1400px; }
        .header { background: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card { border-radius: 8px; border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .btn-sm { padding: 4px 10px; font-size: 12px; }
        .filter-bar { background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; }
        .status-active { background: #d4edda; color: #155724; }
        .status-inactive { background: #f8d7da; color: #721c24; }
        .status-deleted { background: #e2e3e5; color: #383d41; }
        table.dataTable { margin-top: 10px !important; }
        .dataTables_filter input { border: 1px solid #ddd; border-radius: 4px; padding: 6px 12px; }
        .action-buttons { display: flex; gap: 5px; justify-content: center; }
    </style>
</head>
<body>

<div class="container mt-4">
    <!-- Header -->
    <div class="header">
        <h4 class="mb-0">Product Management</h4>
        <p class="text-muted small mb-0">Manage your product inventory</p>
    </div>

    <!-- Action Bar -->
    <div class="filter-bar">
        <div class="row g-3">
            <div class="col-md-3">
                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm w-100">+ Add New Product</a>
            </div>
            <div class="col-md-3">
                <select id="statusFilter" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="deleted">Deleted</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" id="minPrice" class="form-control form-control-sm" placeholder="Min Price">
            </div>
            <div class="col-md-3">
                <input type="text" id="maxPrice" class="form-control form-control-sm" placeholder="Max Price">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col text-end">
                <button id="applyFilters" class="btn btn-secondary btn-sm">Apply Filters</button>
                <button id="resetFilters" class="btn btn-light btn-sm">Reset</button>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0" id="productsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="40">#</th>
                            <th>Product Name</th>
                            <th width="100">Price</th>
                            <th>Description</th>
                            <th width="100">Stock</th>
                            <th width="100">Status</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    let table = $('#productsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            url: "{{ route('products.index') }}",
            data: function(d) {
                d.status_filter = $('#statusFilter').val();
                d.min_price = $('#minPrice').val();
                d.max_price = $('#maxPrice').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'price', name: 'price', render: function(data) { return '$' + parseFloat(data).toFixed(2); } },
            { data: 'description', name: 'description', defaultContent: '-' },
            { data: 'stock_quantity', name: 'stock_quantity', defaultContent: '0' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries"
        }
    });

    // Apply filters
    $('#applyFilters').click(function() {
        table.ajax.reload();
    });

    // Reset filters
    $('#resetFilters').click(function() {
        $('#statusFilter').val('');
        $('#minPrice').val('');
        $('#maxPrice').val('');
        table.ajax.reload();
    });

    // Toggle Status
    $(document).on('change', '.toggle-status', function() {
        let id = $(this).data('id');
        $.post('/products/status/' + id, function() {
            table.ajax.reload();
            showMessage('Status updated', 'success');
        }).fail(function() {
            showMessage('Error updating status', 'error');
        });
    });

    // Delete Product
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        if(confirm('Are you sure you want to delete this product?')) {
            $.ajax({
                url: '/products/delete/' + id,
                type: 'DELETE',
                success: function() {
                    table.ajax.reload();
                    showMessage('Product deleted', 'success');
                },
                error: function() {
                    showMessage('Error deleting product', 'error');
                }
            });
        }
    });

    // Restore Product
    $(document).on('click', '.restore-btn', function() {
        let id = $(this).data('id');
        if(confirm('Restore this product?')) {
            $.post('/products/restore/' + id, function() {
                table.ajax.reload();
                showMessage('Product restored', 'success');
            }).fail(function() {
                showMessage('Error restoring product', 'error');
            });
        }
    });

    // Simple message function
    function showMessage(message, type) {
        let bgColor = type === 'success' ? '#d4edda' : '#f8d7da';
        let textColor = type === 'success' ? '#155724' : '#721c24';
        let msgDiv = $('<div style="position:fixed; top:20px; right:20px; padding:10px 20px; background:'+bgColor+'; color:'+textColor+'; border-radius:5px; z-index:9999;">'+message+'</div>');
        $('body').append(msgDiv);
        setTimeout(function() { msgDiv.fadeOut(function() { $(this).remove(); }); }, 2000);
    }
});
</script>

</body>
</html>