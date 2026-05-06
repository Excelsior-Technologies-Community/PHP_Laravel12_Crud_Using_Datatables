<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::withTrashed()->select('*');

            return Datatables::of($products)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->deleted_at) {
                        return '<span class="badge bg-danger">Deleted</span>';
                    }
                    $checked = $row->status == 'active' ? 'checked' : '';
                    return '<div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input toggle-status" type="checkbox" data-id="'.$row->id.'" '.$checked.' style="cursor:pointer; width:40px; height:20px;">
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    if ($row->deleted_at) {
                        return '<button class="btn btn-success btn-sm restore-btn" data-id="' . $row->id . '">Restore</button>';
                    }
                    return '
                        <div class="action-btns">
                            <a href="' . route('products.show', $row->id) . '" class="btn btn-info btn-sm text-white">Show</a>
                            <a href="' . route('products.edit', $row->id) . '" class="btn btn-primary btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '">Delete</button>
                        </div>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('products.index');
    }

    public function searchSuggestions(Request $request)
    {
        $query = $request->get('query');
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'price']);

        return response()->json($products);
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        Product::create($request->all());
        return redirect()->route('products.index')->with('success', 'Product Added');
    }

    public function edit($id)
    {
        $product = Product::find($id);
        return view('products.edit', compact('product'));
    }

    public function show($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $status = $request->status;
        if (!in_array($status, ['active', 'deleted'])) {
            $status = 'active';
        }
        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'status' => $status,
        ]);
        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->status = 'deleted';
        $product->save();
        $product->delete();
        return response()->json(['success' => true]);
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();
        $product->status = 'active';
        $product->save();
        return response()->json(['success' => true]);
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->status = $product->status == 'active' ? 'inactive' : 'active';
        $product->save();
        return response()->json(['success' => true]);
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }
}