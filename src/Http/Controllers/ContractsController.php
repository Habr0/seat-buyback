<?php

namespace Habr0\Buyback\Http\Controllers;

use Habr0\Buyback\Enums\BasePriceEnum;
use Habr0\Buyback\Models\AllowedSystem;
use Habr0\Buyback\Models\PriceModifier;
use Habr0\Buyback\Models\Settings;
use Habr0\Buyback\Services\PriceService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Seat\Eveapi\Models\Contracts\ContractDetail;
use Seat\Eveapi\Models\Sde\SolarSystem;
use Seat\Web\Http\Controllers\Controller;
use Illuminate\View\View;

class ContractsController extends Controller
{
    public function __construct(private readonly PriceService $priceService)
    {
    }

    public function index(Request $request) : View
    {
        $contracts = ContractDetail::where('title', 'like', 'Buyback: %')->with(['issuer', 'start_location'])->get();

        return view('buyback::contracts', [
            'contracts' => $contracts,
        ]);
    }
}
