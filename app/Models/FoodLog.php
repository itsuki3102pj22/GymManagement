<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'meal_text',
        'total_calories',
        'p_balance',
        'f_balance',
        'c_balance',
        'protein_grams',
        'fat_grams',
        'carbs_grams',
        'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'logged_at' => 'date',
            'total_calories' => 'integer',
            'p_balance' => 'float',
            'f_balance' => 'float',
            'c_balance' => 'float',
            'protein_grams' => 'float',
            'fat_grams' => 'float',
            'carbs_grams' => 'float',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
