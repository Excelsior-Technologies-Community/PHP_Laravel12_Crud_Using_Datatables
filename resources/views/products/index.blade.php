<!DOCTYPE html>
<html>

<head>
    <title>Product List</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #eef2f7, #f8f9fa);
        }

        .card {
            border-radius: 15px;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(45deg, #0d6efd, #0b5ed7);
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }

        table.dataTable thead {
            background-color: #f1f3f5;
        }

        .badge-status {
            font-size: 0.75rem;
            padding: 5px 10px;
            border-radius: 20px;
        }

        /* ACTION BUTTON ALIGNMENT */
        .action-btns {
            display: flex;
            justify-content: center;
            gap: 6px;
        }

        .action-btns .btn {
            padding: 5px 8px;
            border-radius: 6px;
        }

        .btn:hover {
            transform: scale(1.05);
            transition: 0.2s;
        }

        .dataTables_filter input {
            border-radius: 6px;
            padding: 5px 10px;
        }

        .dataTables_paginate .paginate_button {
            border-radius: 6px !important;
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <div class="card shadow">

            <!-- HEADER -->
            <div class="card-header d-flex justify-content-between align-items-center">

                <!-- LEFT SIDE -->
                <div class="d-flex align-items-center gap-2">
                    <i class="fa fa-box fs-5"></i>
                    <h5 class="mb-0">Product List</h5>
                </div>

                <!-- RIGHT SIDE (GROUPED BUTTONS) -->
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('products.create') }}"
                        class="btn btn-light btn-sm d-flex align-items-center gap-1">
                        <i class="fa fa-plus"></i>
                        <span>Add Product</span>
                    </a>

                    <a href="{{ route('products.export') }}"
                        class="btn btn-dark btn-sm d-flex align-items-center gap-1">
                        <i class="fa fa-file-excel"></i>
                        <span>Export</span>
                    </a>
                </div>

            </div>

            <!-- BODY -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="productTable">

                        <!-- FIXED HEADINGS -->
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-start">Name</th>
                                <th class="text-end">Price</th>
                                <th class="text-start">Description</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" width="200">Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        // CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // DataTable
        $('#productTable').DataTable({
            processing: true,
            serverSide: true,
            pagingType: "simple_numbers",
            pageLength: 3,
            lengthMenu: [3, 5, 10, 25],
            ajax: "{{ route('products.index') }}",

            // FIXED ALIGNMENT
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                },
                {
                    data: 'name',
                    className: "text-start"
                },
                {
                    data: 'price',
                    className: "text-end"
                },
                {
                    data: 'description',
                    className: "text-start"
                },
                {
                    data: 'status',
                    className: "text-center"
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                }
            ],

            // STATUS + BUTTON FIX
            drawCallback: function() {
                $('#productTable tbody tr').each(function() {

                    let statusCell = $(this).find('td:eq(4)');
                    let actionCell = $(this).find('td:eq(5)');

                    // STATUS BADGE
                    if (statusCell.text().trim() === 'Active') {
                        statusCell.html('<span class="badge bg-success badge-status">Active</span>');
                    } else {
                        statusCell.html('<span class="badge bg-danger badge-status">Deleted</span>');
                    }

                    // BUTTON ALIGN
                    actionCell.html('<div class="action-btns">' + actionCell.html() + '</div>');
                });
            }
        });


        // DELETE
        $('body').on('click', '.delete', function() {
            if (confirm('Delete product?')) {
                $.ajax({
                    type: 'DELETE',
                    url: '/products/delete/' + $(this).data('id'),
                    success: function() {
                        $('#productTable').DataTable().ajax.reload();
                    }
                });
            }
        });

        // RESTORE
        $('body').on('click', '.restore', function() {
            let id = $(this).data('id');

            $.ajax({
                url: '/products/restore/' + id,
                type: 'POST',
                success: function() {
                    $('#productTable').DataTable().ajax.reload();
                }
            });
        });

        // TOGGLE
        $('body').on('click', '.toggleStatus', function() {
            let id = $(this).data('id');

            $.ajax({
                url: '/products/status/' + id,
                type: 'POST',
                success: function() {
                    $('#productTable').DataTable().ajax.reload();
                }
            });
        });
    </script>

</body>

</html>