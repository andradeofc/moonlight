<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrafficLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'ip_address',
        'country',
        'device_type',
        'browser',
        'user_agent',
        'referrer',
        'destination',
        'reason',
        'request_url', // Novo campo
    ];

    // Definindo o relacionamento com Campaign 
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}