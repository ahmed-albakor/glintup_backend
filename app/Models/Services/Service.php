<?php

namespace App\Models\Services;

use App\Models\Booking\BookingService;
use App\Models\Rewards\FreeService;
use App\Models\Salons\Salon;
use App\Services\LanguageService;
use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use SoftDeletes, HasTranslations, LanguageTrait;

    protected $fillable = [
        'salon_id',
        'name',
        'description',
        'icon',
        'duration_minutes',
        'price',
        'currency',
        'discount_percentage',
        'gender',
        'is_active',
        'is_home_service',
        'is_beautician',
        'capacity',
        'order',
    ];

    protected $casts = [
        'price'               => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_active'           => 'boolean',
        'duration_minutes'    => 'integer',
        'is_home_service'     => 'boolean',
        'is_beautician'       => 'boolean',
        'capacity'            => 'integer',
        'order'               => 'integer',
        'created_at'          => 'datetime',
        'salon_id'           => 'integer',
        'updated_at'          => 'datetime',
        'deleted_at'          => 'datetime',
    ];

    protected $translatable = [
        'name',
        'description',
    ];
    protected $appends = [
        'final_price',
        'icon_url',
    ];

    // relationships
    public function salon()
    {
        return $this->belongsTo(Salon::class)->withTrashed();
    }

    public function bookings()
    {
        return $this->hasMany(BookingService::class);
    }

    public function freeServices()
    {
        return $this->hasMany(FreeService::class);
    }

    public function groupServices()
    {
        return $this->hasMany(GroupService::class);
    }


    // Attributes
    public function getFinalPriceAttribute(): float
    {
        return $this->price - ($this->price * $this->discount_percentage / 100);
    }

    public function getIconUrlAttribute(): string
    {
        return asset('storage/' . $this->icon);
    }


    protected function name(): Attribute
    {
        $Get_Multi_Language = LanguageService::getMultiLanguage();

        return Attribute::make(
            get: fn(string $value) => $Get_Multi_Language ? $this->getAllTranslations('name') : $value,
        );
    }

    protected function description(): Attribute
    {
        $Get_Multi_Language = LanguageService::getMultiLanguage();

        return Attribute::make(
            get: fn(string $value) => $Get_Multi_Language ? $this->getAllTranslations('description') : $value,
        );
    }
}
