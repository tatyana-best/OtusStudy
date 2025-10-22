<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/*
 * \app\Models\Product.php
 */
class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = ['id', 'name', 'art', 'price', 'qantity'];
}
