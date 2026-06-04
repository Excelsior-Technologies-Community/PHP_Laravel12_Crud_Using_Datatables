<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Product([
            'name' => $row['name'],
            'price' => $row['price'],
            'description' => $row['description'] ?? null,
            'status' => $row['status'] ?? 'active',
            'sku' => $row['sku'] ?? null,
            'stock_quantity' => $row['stock_quantity'] ?? 0,
            'brand' => $row['brand'] ?? null,
        ]);
    }
}