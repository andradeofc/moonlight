<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TrafficLog;

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
    'xid', // Certifique-se de que está listado aqui
    'tags',
    'is_active',
    ];
    
    public function domain()
{
    return $this->belongsTo(Domain::class, 'domain_id');
}
    
    public function logs()
    {
        return $this->hasMany(TrafficLog::class);
    }
    public function trafficLogs()
{
    return $this->hasMany(TrafficLog::class);
}

public function getResolvedDomainAttribute()
{
    if (!$this->relationLoaded('domain')) {
        $this->load('domain');
    }

    return is_object($this->domain) ? $this->domain->name : $this->domain;
}


}