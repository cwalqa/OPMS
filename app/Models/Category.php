<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;

class Category extends Model
{
    //

    protected $fillable = ['name', 'description'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
