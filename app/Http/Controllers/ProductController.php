<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::withTrashed()->select('*');
            
            // Apply filters
            if ($request->status_filter && $request->status_filter != '') {
                if ($request->status_filter == 'deleted') {
                    $products->onlyTrashed();
                } else {
                    $products->where('status', $request->status_filter);
                }
            }
            
            if ($request->min_price && $request->min_price != '') {
                $products->where('price', '>=', $request->min_price);
            }
            
            if ($request->max_price && $request->max_price != '') {
                $products->where('price', '<=', $request->max_price);
            }

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('status', function($row) {
                    if ($row->deleted_at) {
                        return '<span class="status-badge status-deleted">Deleted</span>';
                    }
                    $checked = $row->status == 'active' ? 'checked' : '';
                    return '<label style="cursor:pointer;">
                                <input type="checkbox" class="toggle-status" data-id="'.$row->id.'" '.$checked.'>
                                <span class="ms-1">'.ucfirst($row->status).'</span>
                            </label>';
                })
                ->addColumn('action', function($row) {
                    if ($row->deleted_at) {
                        return '<button class="btn btn-success btn-sm restore-btn" data-id="'.$row->id.'">Restore</button>';
                    }
                    return '<div class="action-buttons">
                                <a href="'.route('products.show', $row->id).'" class="btn btn-info btn-sm">View</a>
                                <a href="'.route('products.edit', $row->id).'" class="btn btn-primary btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="'.$row->id.'">Delete</button>
                            </div>';
                })
                ->addColumn('stock_quantity', function($row) {
                    return $row->stock_quantity ?? 0;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        
        return view('products.index');
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);
        
        Product::create($request->all());
        return redirect()->route('products.index')->with('success', 'Product added successfully');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
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
        
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);
        
        $product->update($request->all());
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

    public function searchSuggestions(Request $request)
    {
        $query = $request->get('query');
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'price']);
        return response()->json($products);
    }
}