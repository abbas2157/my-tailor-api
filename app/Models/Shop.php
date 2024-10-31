<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', // Add the id field here
        'shop_name',
        'famous_name',
        'city',
        'shop_address',
        'shop_open_time',
        'shop_close_time',
    ];}
