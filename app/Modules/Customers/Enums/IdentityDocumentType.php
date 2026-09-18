<?php

namespace App\Modules\Customers\Enums;

enum IdentityDocumentType: string
{
    case NationalId = 'national_id';
    case Passport = 'passport';
    case DriverLicense = 'driver_license';
    case ResidencePermit = 'residence_permit';

    public function label(): string
    {
        return match ($this) {
            self::NationalId => __('National ID'),
            self::Passport => __('Passport'),
            self::DriverLicense => __('Driver License'),
            self::ResidencePermit => __('Residence Permit'),
        };
    }
}
