<?php

namespace App;

enum TaxpayerType: string
{
    case NeplatitelDph = 'neplatitel_dph';
    case PlatitelDph = 'platitel_dph';
    case IdentifikovanaOsoba = 'identifikovana_osoba';

    public function label(): string
    {
        return __('app.enums.taxpayer_type.'.$this->value);
    }
}
