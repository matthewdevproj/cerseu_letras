<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'site_name',
        'site_description',
        'logo_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'header_text',
        'footer_text',
        'email',
        'email_admision',
        'telefono',
        'direccion',
        'horario_atencion',
        'facebook',
        'instagram',
        'twitter',
        'linkedin',
        'youtube',
        'tiktok',
        'web_facultad',
        'directorio_facultad',
    ];

    /**
     * Get the site settings (singleton pattern with cache)
     */
    public static function get(): ?self
    {
        return Cache::remember('site_settings', 3600, function () {
            return self::first();
        });
    }

    /**
     * Clear the settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('site_settings');
    }

    /**
     * Get a specific setting value
     */
    public static function getValue(string $key, $default = null)
    {
        $settings = self::get();
        return $settings?->{$key} ?? $default;
    }
}
