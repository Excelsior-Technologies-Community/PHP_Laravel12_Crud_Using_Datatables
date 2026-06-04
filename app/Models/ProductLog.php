<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductLog extends Model
{
    protected $fillable = ['product_id', 'action', 'old_data', 'new_data', 'ip_address'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}