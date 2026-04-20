<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'name',
        'height',
        'gender',
        'birth_date',
        'pal_level',
        'uuid',
        'target_weight',
        'medical_notes',
        'line_user_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date'    => 'date',
            'is_active'     => 'boolean',
            'height'        => 'float',
            'target_weight' => 'float',
            'gender'        => 'integer',
            'pal_level'     => 'integer',
        ];
    }

    // UUID を自動生成
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (Client $client) {
            if (empty($client->uuid)) {
                $client->uuid = (string) Str::uuid();
            }
        });
    }

    // ヘルパー
    public function genderLabel(): string
    {
        return $this->gender === 1 ? '男性' : '女性';
    }

    public function palLabel(): string
    {
        return match($this->pal_level) {
            1 => '低い (I)',
            2 => 'ふつう (II)',
            3 => '高い (III)',
            default => '不明',
        };
    }

    public function getAgeAttribute(): int
    {
        return $this->birth_date->age;
    }

    // リレーション
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function bodyStats()
    {
        return $this->hasMany(BodyStat::class)->orderBy('measured_at');
    }

    public function latestBodyStat()
    {
        return $this->hasOne(BodyStat::class)->latestOfMany('measured_at');
    }

    public function workoutLogs()
    {
        return $this->hasMany(WorkoutLog::class)->orderBy('logged_at');
    }

    public function foodLogs()
    {
        return $this->hasMany(FoodLog::class)->orderBy('logged_at');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}