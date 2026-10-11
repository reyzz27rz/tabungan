<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPet extends Model
{
    protected $fillable = ['user_id', 'pet_name', 'evolution_stage', 'pet_xp'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}