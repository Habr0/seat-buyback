<?php

namespace Habr0\Buyback\Http\Controllers;

use Habr0\Buyback\Enums\BasePriceEnum;
use Habr0\Buyback\Models\AllowedSystem;
use Habr0\Buyback\Models\PriceModifier;
use Habr0\Buyback\Models\Settings;
use Habr0\Buyback\Services\PriceService;
use Habr0\Buyback\Services\SystemService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Seat\Eveapi\Models\Contracts\ContractDetail;
use Seat\Eveapi\Models\Sde\InvMarketGroup;
use Seat\Eveapi\Models\Sde\SolarSystem;
use Seat\Web\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(
        private readonly PriceService $priceService,
        private readonly SystemService $systemService,
    )
    {
    }

    public function index(Request $request) : View
    {
        $marketGroupsTree = $this->priceService->getMarketGroupsTree();

        $defaultPriceModifier = $this->priceService->getDefaultPriceModifier();

        $allowedSystems = AllowedSystem::join(
            'solar_systems',
            'buyback_allowed_systems.system_id',
            '=',
            'solar_systems.system_id'
        )
            ->select('solar_systems.system_id as system_id', 'solar_systems.name as name')
            ->get();

        $settings = Settings::orderByDesc('created_at')->first();

        return view('buyback::admin', [
            'defaultPriceModifier' => $defaultPriceModifier,
            'allowedSystems' => $allowedSystems,
            'settings' => $settings,
            'marketGroupsTree' => $marketGroupsTree,
            'marketGroupModifiers' => $this->indexPriceModifier(),
        ]);
    }

    public function systemSearch(Request $request): ?Collection
    {
        return $this->systemService->searchByNameLike($request->get('search'));
    }

    public function update(Request $request)
    {
        $this->priceService->storeModifier(
            $request->get('defaultPriceModifier'),
            null
        );

        $settings = new Settings();
        $settings->base_price = $request->get('basePrice');
        $settings->instruction = $request->get('instruction');
        $settings->save();

        AllowedSystem::truncate();
        if ($request->get('allowedSystems')) {
            $now = now();
            AllowedSystem::insert(
                collect($request->get('allowedSystems'))
                    ->map(fn($item) => [
                        'system_id' => $item,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])
                    ->toArray()
            );
        }

        return redirect('/buyback/admin')->with('saved', 'Saved!');
    }

    public function storePriceModifier(Request $request): void
    {
        $this->priceService->storeModifier(
            $request->get('priceModifier'),
            $request->get('marketGroup')
        );
    }

    public function deletePriceModifier(Request $request): bool
    {
        if (! $request->get('marketGroup')) {
            return false;
        }

        $this->priceService->storeModifier(
            null,
            $request->get('marketGroup')
        );

        return true;
    }

    public function updatePriceModifier(Request $request): void
    {
        $this->priceService->storeModifier(
            $request->get('priceModifier'),
            $request->get('marketGroup')
        );
    }

    public function indexPriceModifier(): array
    {
        return $this->priceService->getMarketGroupPriceModifiers()->sortBy('group_id')->flatten()->toArray();
    }
}
