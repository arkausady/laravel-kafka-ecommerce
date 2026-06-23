<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Mengizinkan kolom ini diisi secara massal
    protected $fillable = ['user_email', 'total_price', 'status'];
}
