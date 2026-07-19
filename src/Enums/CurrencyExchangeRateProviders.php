<?php

declare(strict_types=1);

namespace Enlivy\Enums;

use Enlivy\Enums\Concern\EnumValues;

enum CurrencyExchangeRateProviders: string
{
    use EnumValues;

    case ECB = 'ecb';
    case BNR = 'bnr';
    case NBP = 'nbp';
    case CNB = 'cnb';
    case MNB = 'mnb';
    case RIKSBANK = 'riksbank';
    case DN = 'dn';
}
