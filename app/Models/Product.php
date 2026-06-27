<?php

namespace App\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, BelongsToTenant, SoftDeletes;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'internal_code',
        'description',
        'price',
        'sale_price',
        'cost_price',
        'variations',
        'images',
        'is_featured',
        'status',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'variations' => 'array',
        'images' => 'array',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    /**
     * Relación con la Categoría.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación con la Marca.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Relación con el Inventario físico (1-a-1).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }
}
