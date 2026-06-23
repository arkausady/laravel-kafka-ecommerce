<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Mengizinkan kolom ini diisi secara massal
    protected $fillable = ['name', 'stock'];
}
