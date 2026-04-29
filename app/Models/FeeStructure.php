<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'amount', 'due_day', 'late_fee_amount', 'late_fee_grace_days', 'is_default'])]
class FeeStructure extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'late_fee_amount' => 'decimal:2',
            'is_default' => 'boolean',
            'due_day' => 'integer',
            'late_fee_grace_days' => 'integer',
        ];
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public static function default(): ?self
    {
        return self::where('is_default', true)->first();
    }
}
