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
