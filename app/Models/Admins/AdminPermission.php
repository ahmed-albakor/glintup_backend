<?php

namespace App\Models\Admins;

use App\Services\LanguageService;
use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class AdminPermission extends Model
{
    use SoftDeletes, LanguageTrait, HasTranslations;

    protected $fillable = ['name', 'key', 'orders'];


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $translatable = ['name'];

    protected function name(): Attribute
    {
        $multi = LanguageService::getMultiLanguage();

        return Attribute::make(
            get: fn(string $value) => $multi ? $this->getAllTranslations('name') : $value,
        );
    }

    public function userAdminPermissions()
    {
        return $this->hasMany(UserAdminPermission::class);
    }
}
