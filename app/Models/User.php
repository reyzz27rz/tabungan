<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'total_savings', 'points', 'level', 'current_xp'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function pet() {
        return $this->hasOne(UserPet::class);
    }

    public function savings() {
        return $this->hasMany(Saving::class);
    }

    public function achievements() {
    return $this->belongsToMany(Achievement::class, 'user_achievements')->withPivot('unlocked_at');
}

    public function withdrawals() {
    return $this->hasMany(Withdrawal::class);
}
}