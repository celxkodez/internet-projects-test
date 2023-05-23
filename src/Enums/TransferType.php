<?php

namespace App\Enums;

enum TransferType: string
{
    case BUY = 'buy';
    case SELL = 'sell';

    public static function getValues(): array
    {
        return TransferType::cases();
    }
}
