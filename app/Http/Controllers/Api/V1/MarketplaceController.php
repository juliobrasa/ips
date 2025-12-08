<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubnetResource;
use App\Models\Subnet;
use App\Repositories\Contracts\SubnetRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MarketplaceController extends Controller
{
    public function __construct(
        protected SubnetRepositoryInterface $subnetRepository
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only([
            'rir', 'country', 'cidr', 'min_price', 'max_price', 'sort', 'direction'
        ]);

        $subnets = $this->subnetRepository->getAvailableForMarketplace(
            $filters,
            $request->get('per_page', 12)
        );

        return SubnetResource::collection($subnets);
    }

    public function show(Subnet $subnet): SubnetResource
    {
        if (!$subnet->isAvailable() || !$subnet->ownership_verified_at) {
            abort(404, __('Subnet not available.'));
        }

        return new SubnetResource($subnet->load('company'));
    }
}
