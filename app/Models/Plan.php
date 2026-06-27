<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'price',
        'billing_period',
        'max_products',
        'max_users',
        'features',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
    ];

    /**
     * Relación con los Tenants que tienen este plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }
}
