<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'stripe_id',
    ];

    protected $hiddden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'integer',
        ];
    }

    // ロール判定
    public function isSupervisor(): bool
    {
        return $this->role === 2;
    }

    public function isTrainer(): bool
    {
        return $this->role === 1;
    }

    // リレーション
    public function clients()
    {
        return $this->hasMany(Client::class, 'trainer_id');
    }

    public function reservation()
    {
        return $this->hasMany(Reservation::class, 'trainer_id');
    }
}
