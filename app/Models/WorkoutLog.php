<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'menu_id',
        'weight',
        'reps',
        'sets',
        'intensity',
        'total_volume',
        'condition_notes',
        'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'logged_at' => 'date',
            'weight' => 'float',
            'total_volume' => 'float',
            'intensity' => 'integer',
        ];
    }

    // total_volumeを自動計算
    protected static function boot(): void
    {
        parent::boot();
        static::saving(function (WorkoutLog $log) {
            $log->total_volume = $log->weight * $log->rep * $log->sets;
        });
    }

    public function intensityLabel(): string
    {
        return match($this->intensity) {
            1 => '弱',
            2 => '中',
            3 => '強',
            default => '不明',
        };
    }

    public function client()
    {
        return $this->belongsTo(client::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
