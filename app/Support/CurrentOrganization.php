<?php

namespace App\Support;

use App\Models\Organization;

class CurrentOrganization
{
    public static function get(): Organization
    {
        return Organization::query()->firstOrFail();
    }
}
