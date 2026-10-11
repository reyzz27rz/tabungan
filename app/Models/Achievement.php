<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = ['title', 'description', 'badge_icon', 'code'];

    public function users() {
        return $this->belongsToMany(User::class, 'user_achievements')->withPivot('unlocked_at');
    }
}