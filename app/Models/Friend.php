<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'friend_id', 'status', 'remarks'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
