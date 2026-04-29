<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Bayarcash = 'bayarcash';
    case ManualCash = 'manual_cash';
    case ManualTransfer = 'manual_transfer';
    case ManualCheque = 'manual_cheque';

    public function label(): string
    {
        return match ($this) {
            self::Bayarcash => 'BayarCash',
            self::ManualCash => 'Cash',
            self::ManualTransfer => 'Bank Transfer',
            self::ManualCheque => 'Cheque',
        };
    }

    public static function manualMethods(): array
    {
        return [self::ManualCash, self::ManualTransfer, self::ManualCheque];
    }
}
