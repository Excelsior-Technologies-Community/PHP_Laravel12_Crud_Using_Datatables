# PHP_Laravel12_Crud_Using_Datatables

---

This project is a Product CRUD system (Create, Read, Update, Delete) in Laravel 12 using DataTables with AJAX for real-time updates. The main features include:

Add, Edit, View, and Delete products.

Soft delete with status update (active or deleted).

Search and pagination in the product listing without page refresh.

Responsive UI using Bootstrap 5.

---

# Project SetUp

---

## STEP 1: Create New Laravel 12 Project

### Run Command :

```
composer create-project laravel/laravel:^12.0 PHP_Laravel12_Crud_Using_Datatables
```

### Go inside project:

```
cd PHP_Laravel12_Crud_Using_Datatables
```

### Run project:

```
php artisan serve

```
 Open browser :

```
http://127.0.0.1:8000
```


## STEP 2: Database Configuration

### Open .env file and update database credentials:

```

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_crud_using_datatable
DB_USERNAME=root
DB_PASSWORD=

```

### Create database:

```
laravel12_crud_using_datatable
```




## STEP 3: Create Model + Migration

Run Command:

```
php artisan make:model Product -m

```

Explaination:

Product model will represent products table.

-m flag creates a migration file to define table structure.




## STEP 4: Migration (products table)

### database/migrations/xxxx_create_products_table.php

```

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create 'products' table
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->string('name'); // Product name
            $table->decimal('price', 10, 2); // Product price with 2 decimal points
            $table->text('description')->nullable(); // Product description, nullable

            // ENUM status: 'active' or 'deleted', default is 'active'
            $table->enum('status', ['active', 'deleted'])->default('active');

            // Soft Delete column 'deleted_at' to allow soft deleting
            $table->softDeletes();

            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop 'products' table if it exists
        Schema::dropIfExists('products');
    }
};

```

### Run migration:

```
php artisan migrate
```

Result: Database table products is created with the above columns.




## STEP 5: Product Model

### File:app/Models/Product.php

```

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    // Enable Soft Deletes functionality
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * This allows us to use methods like Product::create([...])
     * without manually setting each attribute.
     */
    protected $fillable = [
        'name',        // Product name
        'price',       // Product price
        'description', // Product description
        'status'       // Product status (active/deleted)
    ];

    /**
     * The attributes that should be treated as dates.
     * This includes 'deleted_at' because of SoftDeletes.
     */
    protected $dates = ['deleted_at'];
}


```


explaination:

SoftDeletes: Allows you to delete products without removing them permanently.

$fillable: Fields allowed for mass assignment.





## STEP 6: Create Controller

### Run Command:

```

php artisan make:controller ProductController

```

Explaination:

Controller will handle all CRUD operations.



## STEP 7: Controller Logic (AJAX + DataTables)

### File:app/Http/Controllers/ProductController.php

```

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // INDEX (Datatable)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::select('*');

            return datatables()->of($products)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return $row->status ? 'Active' : 'Inactive';
                })
                ->addColumn('action', function ($row) {
                    return '
        <a href="' . route('products.show', $row->id) . '" 
           class="btn btn-info btn-sm me-1">Show</a>

        <a href="' . route('products.edit', $row->id) . '" 
           class="btn btn-primary btn-sm me-1">Edit</a>

        <button class="btn btn-danger btn-sm delete" 
                data-id="' . $row->id . '">Delete</button>
    ';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('products.index');
    }

    // CREATE PAGE
    public function create()
    {
        return view('products.create');
    }

    // STORE
    public function store(Request $request)
    {
        Product::create($request->all());
        return redirect()->route('products.index')->with('success', 'Product Added');
    }

    // EDIT PAGE
    public function edit($id)
    {
        $product = Product::find($id);
        return view('products.edit', compact('product'));
    }

    //Show page

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }



    // UPDATE
    public function update(Request $request, $id)
    {
        Product::find($id)->update($request->all());
        return redirect()->route('products.index')->with('success', 'Product Updated');
    }

    // DELETE (Soft Delete)
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // update status first
        $product->status = 'deleted';
        $product->save();

        // then soft delete
        $product->delete();

        return response()->json(['success' => 'Product deleted']);
    }

}

```

Key Features:

index() — fetches products for DataTables AJAX.

create() / store() — add new product.

edit() / update() — update product.

show() — view product details.

destroy() — soft delete product and update status.

AJAX DataTables:

Enables live search, pagination, and sorting.

No page reload required.





## STEP 8: Routes

### File: routes/web.php

Defines routes for CRUD actions:

```
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Default welcome page route
Route::get('/', function () {
    return view('welcome');
});

// Product Routes

// Show all products (index page)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Show form to create a new product
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');

// Store a new product in the database
Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');

// Show form to edit an existing product
Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');

// Update an existing product in the database
Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('products.update');

// Show details of a single product
Route::get('/products/show/{id}', [ProductController::class, 'show'])->name('products.show');

// Delete a product (soft delete)
Route::delete('/products/delete/{id}', [ProductController::class, 'destroy'])->name('products.delete');


```



## STEP 9: Install DataTables Package (Laravel)

### Run Command:

```
composer require yajra/laravel-datatables-oracle

```
(Optional publish)

### Run Command:

```

php artisan vendor:publish --tag=datatables

```

DataTables: Laravel package to handle server-side AJAX Datatables.


## STEP 10: Create View Folder

Folder: 

```
resources/views/products

```

## STEP 11: Blade File (Datatable + AJAX CRUD)

### Blade files:

index.blade.php → List products with DataTables AJAX.

create.blade.php → Add product form.

edit.blade.php → Edit product form.

show.blade.php → View product details.

AJAX delete allows soft deletion without page reload.



### resources/views/products/index.blade.php

```

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

```

### resources/views/products/create.blade.php

```

<!DOCTYPE html>
<html>

<head>
    <title>Add Product</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-4">
        <div class="card shadow">
            <!-- Card Header -->
            <div class="card-header">
                <h5>Add Product</h5>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <!-- Product Form -->
                <form method="POST" action="{{ route('products.store') }}">
                    @csrf <!-- CSRF token for security -->

                    <!-- Product Name -->
                    <div class="mb-3">
                        <label>Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label>Price</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <!-- Default value is Active -->
                            <option value="active" {{ isset($product) && $product->status == 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ isset($product) && $product->status == 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                    <!-- Submit & Back Buttons -->
                    <button class="btn btn-success">Save</button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>

</body>

</html>

```


### resources/views/products/edit.blade.php

```

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


```


### resources/views/products/show.blade.php

```

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


```


## So You Can See This Type Output:


### Product Page:


<img width="1908" height="959" alt="Screenshot 2025-12-25 122019" src="https://github.com/user-attachments/assets/162256bc-2ec7-468a-96be-863b98d8cc1a" />


### Product Create page:


<img width="1916" height="969" alt="Screenshot 2025-12-25 122551" src="https://github.com/user-attachments/assets/c519de40-4acd-4776-bfc3-05cf879501c8" />


### Product Edit Page:


<img width="1906" height="969" alt="Screenshot 2025-12-25 123144" src="https://github.com/user-attachments/assets/3f20b469-dce0-4ce2-8f3f-462e3e1768fd" />


### Search page(Name):


<img width="1912" height="961" alt="Screenshot 2025-12-25 123213" src="https://github.com/user-attachments/assets/33bf9849-99fe-4854-bc30-cd053fb09df8" />


### Search page(Price):


<img width="1919" height="964" alt="Screenshot 2025-12-25 123229" src="https://github.com/user-attachments/assets/1a4f3201-8e3c-4ef4-92c2-763b09d7b41d" />


### Search page(Description):


<img width="1916" height="956" alt="Screenshot 2025-12-25 123252" src="https://github.com/user-attachments/assets/30675fe8-f436-48d1-a68d-f3ca62f4d6d4" />


### Pagination page(Page-1):


<img width="1919" height="964" alt="Screenshot 2025-12-25 123044" src="https://github.com/user-attachments/assets/f0b8e822-ab07-4d82-b99b-b1a4159c2429" />



### Pagination page(Page-2):


<img width="1919" height="960" alt="Screenshot 2025-12-25 123055" src="https://github.com/user-attachments/assets/1772faba-6742-44e5-93a7-19a0020f164a" />



### Product Delete page:


<img width="1917" height="958" alt="Screenshot 2025-12-25 123335" src="https://github.com/user-attachments/assets/932d98fa-fe9a-4f17-8bb2-7ac251d8b0e3" />



<img width="1911" height="970" alt="Screenshot 2025-12-25 131534" src="https://github.com/user-attachments/assets/422e80d0-d497-4cce-9f5b-60ade7e98762" />




---




# Project Folder Structure:

```

PHP_Laravel12_Crud_Using_Datatables/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── ProductController.php
│   └── Models/
│       └── Product.php
│
├── database/
│   └── migrations/
│       └── xxxx_create_products_table.php
│
├── resources/
│   └── views/
│       └── products/
│           ├── index.blade.php
│           ├── create.blade.php
│           ├── edit.blade.php
│           └── show.blade.php
│
├── routes/
│   └── web.php
│
├── .env
├── composer.json
└── README.md
```
