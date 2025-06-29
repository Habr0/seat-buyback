<?php

namespace Habr0\Buyback\Services;

use Habr0\Buyback\Models\PriceModifier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Seat\Eveapi\Models\Sde\InvMarketGroup;

class PriceService
{
    public function getPriceModifierByDate(\DateTime $date): PriceModifier
    {
        return PriceModifier::where('created_at', '>=', $date->format('Y-m-d H:i:s'))->first();
    }

    public function getAppraisal(string $data): array
    {
        $janiceResponse = Http::withHeaders([
            'Content-Type' => 'text/plain',
            'X-ApiKey' => env('JANICE_KEY'),
        ])
            ->withQueryParameters([
                'market' => 2,
                'persist' => 'true',
                'compactize' => 'true',
                'pricePercentage' => 1
            ])
            ->withBody($data, 'text/plain')
            ->post('https://janice.e-351.com/api/rest/v2/appraisal');

        if (! $janiceResponse->successful()) {
            throw new \Exception('Janice API returned error: ' . $janiceResponse->status());
        }

        return $janiceResponse->json();
    }

    public function getMarketGroupPriceModifiers(\DateTime $date = null): Collection
    {
        $tableName = (new PriceModifier)->getTable();

        $subQuery = PriceModifier::select('group_id', DB::raw('MAX(created_at) as max_created_at'))
            ->when($date, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->groupBy('group_id');

        $modifiers = PriceModifier::joinSub($subQuery, 'latest', function (JoinClause $join) use ($tableName) {
            $join->on("$tableName.group_id", '=', 'latest.group_id')
                ->on("$tableName.created_at", '=', 'latest.max_created_at');
        })
            ->with('invMarketGroup')
            ->get();

        $modifiers = $modifiers->filter(function ($modifier) {
            return $modifier->modifier !== null;
        });

        return $modifiers;
    }

    public function getDefaultPriceModifier(\DateTime $date = null): PriceModifier
    {
        return PriceModifier::whereNull('group_id')
            ->when($date, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->orderByDesc('created_at')
            ->first();
    }

    public function getMarketGroupsTree(): array
    {
        return Cache::remember(
            'market_groups_tree',
            86400, // 1 day
            function () {
                $mainMarketGroups = InvMarketGroup::whereNull('parentGroupID')->get();

                $data = [];
                foreach ($mainMarketGroups as $marketGroup) {
                    $data = $data + $this->getMarketGroupsFlat($marketGroup->marketGroupID);
                }

                return $data;
            }
        );
    }

    public function getMarketGroupsFlat(int $marketGroupId, string $prefix = ''): array
    {
        $mainParent = $this->getById($marketGroupId);
        $currentName = $mainParent->marketGroupName;
        $fullPath = $prefix ? $prefix . ' // ' . $currentName : $currentName;

        $result = [];

        $children = $this->getByParentId($marketGroupId);

        $result[$mainParent->marketGroupID] = $fullPath;
        if (count($children) === 0) {
            // It's a leaf node, store its breadcrumb path
        } else {
            foreach ($children as $child) {
                $result += $this->getMarketGroupsFlat($child->marketGroupID, $fullPath);
            }
        }

        return $result;
    }

    public function getById(int $marketGroupId): object
    {
        return InvMarketGroup::where('marketGroupID', $marketGroupId)->first();
    }

    public function getByParentId(int $marketGroupId): object
    {
        return InvMarketGroup::where('parentGroupID', $marketGroupId)->get();
    }

    public function getParentMarketGroup(int $marketGroupId): object
    {
        return InvMarketGroup::where('marketGroupID', $marketGroupId)->first();
    }

    public function storeModifier(?float $modifier, ?int $marketGroupId): void
    {
        $priceModifier = new PriceModifier();
        $priceModifier->modifier = $modifier;
        $priceModifier->group_id = $marketGroupId;
        $priceModifier->save();
    }
}
