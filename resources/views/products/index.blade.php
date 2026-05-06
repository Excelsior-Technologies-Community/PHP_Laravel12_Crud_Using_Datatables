<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- JS Dependencies -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { background: #f8f9fa; }
        .card { border-radius: 15px; border: none; }
        .card-header { background: linear-gradient(45deg, #0d6efd, #0b5ed7); color: white; padding: 1rem; }
        
        .dataTables_filter { position: relative; }
        .dataTables_filter input {
            width: 350px !important;
            height: 40px;
            border-radius: 20px !important;
            padding-left: 15px !important;
            border: 2px solid #0d6efd !important;
        }

        #search-suggestions {
            position: absolute;
            top: 45px;
            right: 0;
            width: 350px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            display: none;
        }
        .suggestion-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        .suggestion-item:last-child { border-bottom: none; }
        .suggestion-item:hover { background: #f1f3f5; }
        .suggestion-item .price { font-size: 0.8rem; color: #28a745; font-weight: bold; }

        .action-btns { display: flex; justify-content: center; gap: 5px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-boxes"></i> Product Inventory</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('products.create') }}" class="btn btn-light btn-sm fw-bold">Add Product</a>
                <a href="{{ route('products.export') }}" class="btn btn-dark btn-sm fw-bold">Export</a>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center" id="productTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th width="220">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Suggestions Box -->
<div id="search-suggestions"></div>

<script>
    $(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // SweetAlert Toast Configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        let table = $('#productTable').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 0,
            ajax: "{{ route('products.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name', className: "text-start fw-bold" },
                { data: 'price', name: 'price', className: "text-end" },
                { data: 'description', name: 'description', className: "text-start" },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: { 
                search: "", 
                searchPlaceholder: "Search products instantly..." 
            }
        });

        const suggestionBox = $('#search-suggestions');
        
        // Amazon Style Live Search Suggestions
        $(document).on('keyup', '.dataTables_filter input', function() {
            let val = $(this).val();
            let inputPos = $(this).offset();

            if (val.length > 0) {
                $.ajax({
                    url: "{{ route('products.suggestions') }}",
                    data: { query: val },
                    success: function(data) {
                        let html = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                html += `<div class="suggestion-item" data-name="${item.name}">
                                            <div>${item.name}</div>
                                            <div class="price">₹${item.price}</div>
                                         </div>`;
                            });
                            suggestionBox.html(html).css({
                                top: inputPos.top + 45,
                                left: inputPos.left
                            }).show();
                        } else {
                            suggestionBox.hide();
                        }
                    }
                });
            } else {
                suggestionBox.hide();
            }
        });

        // Click suggestion to search
        $(document).on('click', '.suggestion-item', function() {
            let name = $(this).data('name');
            $('.dataTables_filter input').val(name);
            table.search(name).draw();
            suggestionBox.hide();
        });

        // Hide suggestions on outside click
        $(document).click(function(e) {
            if (!$(e.target).closest('.dataTables_filter, #search-suggestions').length) {
                suggestionBox.hide();
            }
        });

        // Toggle Status with SweetAlert Toast
        $(document).on('change', '.toggle-status', function() {
            let id = $(this).data('id');
            $.ajax({
                url: '/products/status/' + id,
                type: 'POST',
                success: function(response) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Status has been updated!'
                    });
                },
                error: function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'Something went wrong!'
                    });
                }
            });
        });

        // Delete with SweetAlert Confirmation
        $(document).on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "This product will be moved to trash!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/products/delete/' + id,
                        type: 'DELETE',
                        success: function() {
                            table.ajax.reload(null, false);
                            Swal.fire(
                                'Deleted!',
                                'Product has been deleted.',
                                'success'
                            );
                        },
                        error: function() {
                            Swal.fire('Error!', 'Could not delete product.', 'error');
                        }
                    });
                }
            });
        });

        // Restore with SweetAlert Toast
        $(document).on('click', '.restore-btn', function() {
            let id = $(this).data('id');
            $.ajax({
                url: '/products/restore/' + id,
                type: 'POST',
                success: function() {
                    table.ajax.reload(null, false);
                    Toast.fire({
                        icon: 'success',
                        title: 'Product restored successfully!'
                    });
                },
                error: function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'Restore failed!'
                    });
                }
            });
        });
    });
</script>
</body>
</html>