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
