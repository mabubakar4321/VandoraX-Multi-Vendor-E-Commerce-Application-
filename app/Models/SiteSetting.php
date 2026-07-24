<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiteSetting extends Model
{
    use HasFactory;
     protected $table = 'site_settings';

     protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'is_public',
        'group',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function getValue()
    {
        return match ($this->type) {
            'boolean' => $this->value === '1' || $this->value === 'true',
            'integer' => (int)$this->value,
            'array' => json_decode($this->value, true),
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    public function setValue($value)
    {
        if (is_array($value) || is_object($value)) {
            $this->value = json_encode($value);
        } else {
            $this->value = $value;
        }
        return $this;
    }


    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

     public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->getValue() : $default;
    }

     public static function set($key, $value, $type = 'string', $group = 'general')
    {
        $setting = static::firstOrCreate(['key' => $key]);
        $setting->value = is_array($value) || is_object($value) ? json_encode($value) : $value;
        $setting->type = $type;
        $setting->group = $group;
        $setting->save();
        return $setting;
    }
    
}
