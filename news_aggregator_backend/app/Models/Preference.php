<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    use HasFactory;

    protected $table = 'preferences'; // Set the table name

    protected $fillable = [
        'user_id',
        'preference_data',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
