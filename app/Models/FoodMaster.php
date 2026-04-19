<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodMaster extends Model
{
    use HasFactory;

    protected $table = 'food_master';

    protected $fillable = [
        'food_name',
        'calories',
        'protein',
        'fat',
        'carb',
    ];

    protected function casts(): array
    {
        return [
            'calories' => 'integer',
            'protein' => 'float',
            'fat' => 'float',
            'carb' => 'float',
        ];
    }
}
