<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $columns;

    public function __construct($columns = null)
    {
        $this->columns = $columns ?: ['id', 'name', 'price', 'description', 'status'];
    }

    public function collection()
    {
        return Product::withTrashed()->get();
    }

    public function headings(): array
    {
        $headings = [];
        foreach ($this->columns as $column) {
            $headings[] = ucfirst(str_replace('_', ' ', $column));
        }
        return $headings;
    }

    public function map($product): array
    {
        $row = [];
        foreach ($this->columns as $column) {
            if ($column == 'status') {
                $row[] = $product->deleted_at ? 'deleted' : $product->status;
            } else {
                $row[] = $product->$column;
            }
        }
        return $row;
    }
}