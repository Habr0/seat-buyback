<?php

namespace Habr0\Buyback\Services;

use Habr0\Buyback\Models\PriceModifier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Seat\Eveapi\Models\Sde\InvMarketGroup;
use Seat\Eveapi\Models\Sde\SolarSystem;

class SystemService
{
    public function searchByNameLike(string $name): ?Collection
    {
        return SolarSystem::where('name', 'like', "%{$name}%")->get();
    }
}
