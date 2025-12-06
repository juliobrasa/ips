<?php

namespace App\Http\Controllers;

use App\Models\Subnet;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Subnet::available()
            ->verified()
            ->clean()
            ->with('company');

        // Filters
        if ($request->filled('rir')) {
            $query->byRir($request->rir);
        }

        if ($request->filled('country')) {
            $query->byCountry($request->country);
        }

        if ($request->filled('cidr')) {
            $query->byCidr($request->cidr);
        }

        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->priceRange($request->min_price, $request->max_price);
        }

        if ($request->filled('search')) {
            $query->where('ip_address', 'like', $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort', 'price_asc');
        switch ($sortBy) {
            case 'price_desc':
                $query->orderBy('price_per_ip_monthly', 'desc');
                break;
            case 'reputation':
                $query->orderBy('reputation_score', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price_asc':
            default:
                $query->orderBy('price_per_ip_monthly', 'asc');
                break;
        }

        $subnets = $query->paginate(12)->withQueryString();

        // Get filter options
        $rirs = ['RIPE', 'ARIN', 'LACNIC', 'APNIC', 'AFRINIC'];
        $cidrs = [24, 23, 22, 21, 20];
        $countries = Subnet::available()
            ->whereNotNull('geolocation_country')
            ->distinct()
            ->pluck('geolocation_country')
            ->sort();

        return view('marketplace.index', compact('subnets', 'rirs', 'cidrs', 'countries'));
    }

    public function show(Subnet $subnet)
    {
        if (!$subnet->isAvailable()) {
            abort(404);
        }

        $subnet->load('company');

        return view('marketplace.show', compact('subnet'));
    }
}
