<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'is-available',
    ];
    public function category() {
        return $this->belongsTo(Category::class);
    }
}
