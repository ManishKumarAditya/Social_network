<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'profile_image', 'country_code', 'address', 'token'
    ];

    public function user(){
        return $this->morphOne(User::class, 'profile');
    }
}
