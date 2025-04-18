<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable. 
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'clicks',
        'domains',
        'extra_clicks_price',
        'traffic_sources',
        'stripe_product_id',
        'stripe_price_id',
        'payment_url',
        'perfect_pay_id',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'traffic_sources' => 'array',
        'price' => 'float',
        'clicks' => 'integer',
        'domains' => 'integer',
        'extra_clicks_price' => 'float',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users with this plan.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'current_plan_id');
    }
}