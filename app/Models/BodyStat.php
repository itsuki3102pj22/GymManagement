<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodyStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'weight',
        'body_fat_percentage',
        'muscle_mass',
        'bmi',
        'measured_at',
    ];

    protected function casts(): array
    {
        return [
            'measured_at'         => 'date',
            'weight'              => 'float',
            'body_fat_percentage' => 'float',
            'muscle_mass'         => 'float',
            'bmi'                 => 'float',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}