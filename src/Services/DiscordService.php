<?php

namespace Habr0\Buyback\Services;

use Habr0\Buyback\Models\PriceModifier;
use Habr0\Buyback\Models\Settings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Seat\Eveapi\Models\Sde\InvMarketGroup;

class DiscordService
{
    public function sendNotification(string $code, int $price, string $contractorName, int $contractorId): PriceModifier
    {
        Http::post(
            env('BUYBACK_DISCORD_HOOK'),
            json_decode('{
              "content": null,
              "embeds": [
                {
                  "title": "New Buyback contract detected!",
                  "description": "**Code:** ['.$code.'](https://janice.e-351.com/a/'.$code.')\n**Price:** '.$price.' ISK",
                  "url": "http://localhost/buyback/contracts",
                  "color": 3760383,
                  "footer": {
                    "text": "'.$contractorName.'",
                    "icon_url": "https://images.evetech.net/characters/'.$contractorId.'/portrait?size=128"
                  }
                }
              ],
              "username": "Buyback Bot",
              "avatar_url": "https://images.evetech.net/types/30768/icon?size=128",
              "attachments": []
            }')
        );
    }
}
