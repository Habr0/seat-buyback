<?php

namespace Habr0\Buyback\Models;

use Habr0\Buyback\Enums\BasePriceEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $table = 'buyback_settings';

    protected $casts = [
        'base_price' => BasePriceEnum::class,
    ];
}
