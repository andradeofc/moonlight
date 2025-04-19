<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TrafficLog;
use App\Models\Domain;

class Campaign extends Model
{
    protected $fillable = [
        'name',
        'domain_id',
        'language',
        'traffic_source',
        'safe_url',
        'safe_method',
        'offer_url',
        'offer_method',
        'countries',
        'devices',
        'token',
        'unique_id',
        'unique_params',
        'xid',
        'tags',
        'is_active',
    ];
    
    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
    
    public function logs()
    {
        return $this->hasMany(TrafficLog::class);
    }
    
    public function trafficLogs()
    {
        return $this->hasMany(TrafficLog::class);
    }
    public function user()
{
    return $this->belongsTo(User::class);
}
}