<?php

declare(strict_types=1);

namespace Enlivy\Enums\Tax;

use Enlivy\Enums\Concern\EnumValues;

enum ProductTaxCategories: string
{
    use EnumValues;

    case GENERAL_SERVICES = 'general_services';
    case DIGITAL_SERVICES = 'digital_services';
    case GENERAL_PHYSICAL_GOODS = 'general_physical_goods';
    case FOODSTUFFS = 'foodstuffs';
    case PRINTED_BOOKS_PERIODICALS = 'printed_books_periodicals';
    case EBOOKS_DIGITAL_PUBLICATIONS = 'ebooks_digital_publications';
    case PHARMACEUTICALS_MEDICAL_GOODS = 'pharmaceuticals_medical_goods';
    case ACCOMMODATION = 'accommodation';
    case RESTAURANT_CATERING = 'restaurant_catering';
    case PASSENGER_TRANSPORT = 'passenger_transport';
    case EVENT_ADMISSION = 'event_admission';
    case EDUCATION_TUITION = 'education_tuition';
    case HEALTHCARE_SERVICES = 'healthcare_services';
    case FINANCIAL_INSURANCE = 'financial_insurance';
}
