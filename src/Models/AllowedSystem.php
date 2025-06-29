<?php

namespace Habr0\Buyback\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowedSystem extends Model
{
    use HasFactory;

    protected $table = 'buyback_allowed_systems';
}
