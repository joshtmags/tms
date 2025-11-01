<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum LangCode: string
{
    use EnumValues;

    case EN = "en";
    case FR = "fr";
    case ES = "es";
}
