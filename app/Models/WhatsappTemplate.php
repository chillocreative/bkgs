<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'name', 'body_template', 'is_active'])]
class WhatsappTemplate extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public static function findByKey(string $key): ?self
    {
        return static::where('key', $key)->where('is_active', true)->first();
    }
}
