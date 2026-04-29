<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

#[Fillable(['key', 'value', 'encrypted'])]
class Setting extends Model
{
    protected function casts(): array
    {
        return ['encrypted' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saved(fn (self $s) => Cache::forget(self::cacheKey($s->key)));
        static::deleted(fn (self $s) => Cache::forget(self::cacheKey($s->key)));
    }

    public static function cacheKey(string $key): string
    {
        return 'settings.'.$key;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever(self::cacheKey($key), function () use ($key, $default) {
            $row = static::where('key', $key)->first();
            if (! $row) {
                return $default;
            }
            $val = $row->value;
            if ($row->encrypted && $val !== null && $val !== '') {
                try {
                    $val = Crypt::decryptString($val);
                } catch (\Throwable $e) {
                    return $default;
                }
            }
            return $val ?? $default;
        });
    }

    public static function set(string $key, ?string $value, bool $encrypted = false): self
    {
        $stored = $value;
        if ($encrypted && $value !== null && $value !== '') {
            $stored = Crypt::encryptString($value);
        }
        $row = static::updateOrCreate(['key' => $key], ['value' => $stored, 'encrypted' => $encrypted]);
        Cache::forget(self::cacheKey($key));
        return $row;
    }

    public static function forget(string $key): void
    {
        static::where('key', $key)->delete();
        Cache::forget(self::cacheKey($key));
    }
}
