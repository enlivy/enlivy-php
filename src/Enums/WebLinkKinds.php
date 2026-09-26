<?php

declare(strict_types=1);

namespace Enlivy\Enums;

use Enlivy\Enums\Concern\EnumValues;

enum WebLinkKinds: string
{
    use EnumValues;

    case WEBSITE = 'website';
    case DIRECTORY = 'directory';
    case LINKEDIN = 'linkedin';
    case FACEBOOK = 'facebook';
    case INSTAGRAM = 'instagram';
    case X = 'x';
    case YOUTUBE = 'youtube';
    case TIKTOK = 'tiktok';
    case PINTEREST = 'pinterest';
    case GOOGLE_BUSINESS = 'google_business';
    case YELP = 'yelp';
    case TRUSTPILOT = 'trustpilot';
    case CRUNCHBASE = 'crunchbase';
    case GITHUB = 'github';
}
