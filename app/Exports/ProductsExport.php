<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    // DATA
    public function collection()
    {
        return Product::select('id','name','price','description','status')->get();
    }

    // HEADINGS
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Price',
            'Description',
            'Status'
        ];
    }
}