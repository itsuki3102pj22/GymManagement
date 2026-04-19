<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'meal_taxt',
        'total_calories',
        'p_barance',
        'f_barance',
        'c_barance',
        'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'logged_at' => 'date',
            'total_calories' => 'integer',
            'p_barance' => 'float',
            'f_barance' => 'float',
            'c_barance' => 'float',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
