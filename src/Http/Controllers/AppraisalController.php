<?php

namespace Habr0\Buyback\Http\Controllers;

use Habr0\Buyback\Models\AllowedSystem;
use Habr0\Buyback\Models\PriceModifier;
use Habr0\Buyback\Models\Settings;
use Habr0\Buyback\Services\PriceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Seat\Eveapi\Models\Sde\InvMarketGroup;
use Seat\Eveapi\Models\Sde\InvType;
use Seat\Web\Http\Controllers\Controller;
use Illuminate\View\View;

class AppraisalController extends Controller
{
    public function __construct(private readonly PriceService $priceService)
    {
    }

    public function index() : View
    {
        $priceModifiers = PriceModifier::whereNull('group_id')->orderByDesc('created_at')->first();
        $allowedSystems = AllowedSystem::join(
            'solar_systems',
            'buyback_allowed_systems.system_id',
            '=',
            'solar_systems.system_id'
        )
            ->select('solar_systems.system_id as system_id', 'solar_systems.name as name')
            ->get();
        $settings = Settings::orderByDesc('created_at')->first();

        return view('buyback::appraisal', [
            'priceModifier' => $priceModifiers,
            'allowedSystems' => $allowedSystems,
            'settings' => $settings,
        ]);
    }

    public function store(Request $request)
    {
        $appraisal = $this->priceService->getAppraisal($request->input('trash'));

        $defaultPriceModifiers = PriceModifier::whereNull('group_id')->orderByDesc('created_at')->first();
        $settings = Settings::orderByDesc('created_at')->first();
        $basePrice = $settings['base_price']->janiceItemKey();
        $marketGroupModifiers = $this->priceService->getMarketGroupPriceModifiers();

        $items = collect();

        foreach ($appraisal['items'] as $item) {
            $invType = InvType::find($item['itemType']['eid']);
            $marketGroup = InvMarketGroup::find($invType->marketGroupID);

            $marketGroups = [];
            $marketGroups[] = $marketGroup->marketGroupID;

            $level = 0;
            while(10 >= $level) {
                $parentId = $marketGroup->parentGroupID;
                if (! $parentId) {
                    break;
                }
                $marketGroup = $this->priceService->getParentMarketGroup($marketGroup->parentGroupID);
                $marketGroups[] = $marketGroup->marketGroupID;
                $level++;
            }

            foreach ($marketGroups as $marketGroupId) {
                $modifier = $marketGroupModifiers->where('group_id', $marketGroupId)->first();
                if ($modifier) {
                    break;
                }
            }

            $modifier = $modifier ?? $defaultPriceModifiers;
            $originalPrice = $item['effectivePrices'][$basePrice];

            $items->push([
                'name' => $item['itemType']['name'],
                'typeId' => $item['itemType']['eid'],
                'quantity' => $item['amount'],
                'modifier' => [
                    'modifier' => $modifier->modifier,
                    'marketGroupId' => $modifier->group_id,
                ],
                'originalPrice' => $originalPrice,
                'modifiedPrice' => (int) round($originalPrice * $modifier->modifier),
            ]);
        }

        return [
            'code' => $appraisal['code'],
            'price' => $items->sum('modifiedPrice'),
            'items' => $items,
        ];
    }
}
