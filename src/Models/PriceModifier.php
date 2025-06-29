<?php

namespace Habr0\Buyback\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Seat\Eveapi\Models\Sde\InvMarketGroup;

class PriceModifier extends Model
{
    use HasFactory;

    protected $table = 'buyback_price_modifiers';

    public function invMarketGroup(): BelongsTo|InvMarketGroup
    {
        return $this->belongsTo(InvMarketGroup::class, 'group_id', 'marketGroupID');
    }
}
