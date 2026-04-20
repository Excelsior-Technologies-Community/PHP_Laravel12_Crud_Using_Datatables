<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;

class ProductController extends Controller
{
    // INDEX (Datatable)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::withTrashed()->select('*'); // include deleted

            return datatables()->of($products)
                ->addIndexColumn()

                ->addColumn('status', function ($row) {
                    return $row->status == 'active' ? 'Active' : 'Deleted';
                })

                ->addColumn('action', function ($row) {

                    // If deleted → show restore button
                    if ($row->deleted_at) {
                        return '
                            <button class="btn btn-success btn-sm restore" 
                                    data-id="' . $row->id . '">Restore</button>
                        ';
                    }

                    return '
                        <a href="' . route('products.show', $row->id) . '" 
                           class="btn btn-info btn-sm me-1">Show</a>

                        <a href="' . route('products.edit', $row->id) . '" 
                           class="btn btn-primary btn-sm me-1">Edit</a>

                        <button class="btn btn-warning btn-sm toggleStatus me-1" 
                                data-id="' . $row->id . '">Toggle</button>

                        <button class="btn btn-danger btn-sm delete" 
                                data-id="' . $row->id . '">Delete</button>
                    ';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('products.index');
    }

    // CREATE
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

    // EDIT
    public function edit($id)
    {
        $product = Product::find($id);
        return view('products.edit', compact('product'));
    }

    // SHOW
    public function show($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        return view('products.show', compact('product'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Only allow these values
        $status = $request->status;
        if (!in_array($status, ['active', 'deleted'])) {
            $status = 'active'; // default fallback
        }

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'status' => $status,
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    // DELETE (Soft Delete)
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->status = 'deleted';
        $product->save();

        $product->delete();

        return response()->json(['success' => 'Product deleted']);
    }

    //  RESTORE
    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        $product->restore();
        $product->status = 'active';
        $product->save();

        return response()->json(['success' => 'Product restored']);
    }

    //  TOGGLE STATUS
    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);

        $product->status = $product->status == 'active' ? 'deleted' : 'active';
        $product->save();

        return response()->json(['success' => 'Status updated']);
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }
}
