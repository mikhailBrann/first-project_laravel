<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    //связываем модель с таблицей в БД
    protected $table = 'products';
    //указываем поля, которые будут заполняться при работе с моделью
    protected $fillable = [
        'article',
        'name',
        'status',
        'data'
    ];
}
