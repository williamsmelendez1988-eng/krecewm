<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'plan_id',
        'name',
        'subdomain',
        'custom_domain',
        'logo',
        'banner',
        'primary_color',
        'secondary_color',
        'contact_email',
        'contact_phone',
        'address',
        'city',
        'social_links',
        'schedule',
        'status',
        'trial_ends_at',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'social_links' => 'array',
        'schedule' => 'array',
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Relación con el Plan de suscripción.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Relación con los usuarios administrativos del Tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relación con las categorías del Tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Relación con las marcas del Tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    /**
     * Relación con los productos del Tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relación con los clientes de la tienda del Tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Relación con los pedidos del Tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relación con la tabla de configuraciones dynamic settings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    /**
     * Relación con el historial de suscripciones.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Obtiene una configuración específica por su key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        $setting = $this->settings()->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}
