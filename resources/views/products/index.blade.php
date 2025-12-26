<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #f8f9fa; /* Light gray background */
        }
        .card {
            border-radius: 12px; /* Rounded card corners */
        }
        .card-header {
            background-color: #0d6efd; /* Bootstrap primary color */
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }
        table.dataTable thead {
            background-color: #e9ecef; /* Light header background for table */
        }
        .badge-status {
            font-size: 0.85rem;
            font-weight: 500;
        }
        /* Pagination button styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.25rem 0.6rem;
            margin: 1px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        /* Search input styling */
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 4px 8px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card shadow-sm">
        <!-- Card Header -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Product List</span>
            <a href="{{ route('products.create') }}" class="btn btn-success btn-sm">
                + Add Product
            </a>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="productTable">
                    <thead class="text-center">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
// Set CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});

// Initialize DataTable
$('#productTable').DataTable({
    processing: true,  // Show processing indicator
    serverSide: true,  // Enable server-side processing
    pagingType: "simple_numbers",
    pageLength: 3,          // Number of rows per page
    lengthMenu: [3,5,10,25], // User can select page length
    ajax: "{{ route('products.index') }}", // Server-side URL
    columns: [
        {data: 'DT_RowIndex', orderable:false, searchable:false, className: "text-center"}, // Serial number
        {data: 'name'},           // Product Name
        {data: 'price', className: "text-end"}, // Price aligned right
        {data: 'description'},    // Description
        {data: 'status', searchable:false, className: "text-center"}, // Status
        {data: 'action', orderable:false, searchable:false, className: "text-center"} // Action buttons
    ],
    // Render status badges after table draw
    drawCallback: function(settings){
        $('#productTable tbody tr').each(function(){
            let statusCell = $(this).find('td:eq(4)');
            if(statusCell.text().trim() === 'Active'){
                statusCell.html('<span class="badge bg-success badge-status">Active</span>');
            } else {
                statusCell.html('<span class="badge bg-danger badge-status">Inactive</span>');
            }
        });
    }
});

// DELETE product
$('body').on('click','.delete',function(){
    if(confirm('Delete product?')){
        $.ajax({
            type:'DELETE',
            url:'/products/delete/'+$(this).data('id'), // Delete route
            success:function(){
                $('#productTable').DataTable().ajax.reload(); // Reload table after delete
            }
        });
    }
});
</script>

</body>
</html>
