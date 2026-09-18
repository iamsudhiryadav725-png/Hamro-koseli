<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingAddress extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'shipping_addresses';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'label',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'phone',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * A shipping address belongs to one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A shipping address can be used in many orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    // -------------------------------------------------------------------------
    // Accessors / Helpers
    // -------------------------------------------------------------------------

    /**
     * Returns the full address as a single formatted string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->province,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Mark this address as the user's default, unsetting any previous default.
     */
    public function setAsDefault(): void
    {
        // Clear existing default for this user
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    /**
     * Parse full address string into province and city components.
     */
    public static function parseAddressComponents(string $fullAddress): array
    {
        $parts = array_map('trim', explode(',', $fullAddress));

        $province = 'N/A';
        $city = 'N/A';

        if (count($parts) >= 3) {
            $province = $parts[0];
            $city = $parts[1];
        } elseif (count($parts) === 2) {
            $province = $parts[0];
            $city = $parts[1];
        } elseif (count($parts) === 1 && ! empty($parts[0])) {
            $city = $parts[0];
        }

        return [
            'province' => mb_substr($province, 0, 80),
            'city' => mb_substr($city, 0, 80),
            'address' => $fullAddress,
        ];
    }

    /**
     * Save an address as the user's default shipping address and touch timestamps.
     */
    public static function saveAsDefault(int $userId, string $address, ?string $phone = null): self
    {
        $parsed = self::parseAddressComponents($address);

        self::where('user_id', $userId)->update(['is_default' => 0]);

        $shippingAddress = self::updateOrCreate(
            ['user_id' => $userId, 'address' => $address],
            [
                'phone' => $phone,
                'city' => $parsed['city'],
                'province' => $parsed['province'],
                'country' => 'Nepal',
                'is_default' => 1,
            ]
        );

        $shippingAddress->touch();

        return $shippingAddress;
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Scope to the default address only.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope by label (e.g. 'Home', 'Office').
     */
    public function scopeOfLabel($query, string $label)
    {
        return $query->where('label', $label);
    }
}
