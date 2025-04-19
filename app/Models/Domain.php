<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cname_record',
        'verified',
    ];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function user()
{
    return $this->belongsTo(User::class);
}
}