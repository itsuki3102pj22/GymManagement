<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'is_suctom',
    ];

    protected function casts(): array
    {
        return [
            'is_cuntom' => 'boolean',
        ];
    }

    public function workoutLogs()
    {
        return $this->hasMany(WorkoutLog::class);
    }
}
