<?php

namespace App\Http\Controllers;

use App\Services\LookingGlassService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class LookingGlassController extends Controller
{
    public function __construct(protected LookingGlassService $lookingGlass)
    {
    }

    /**
     * Show looking glass interface
     */
    public function index(): View
    {
        return view('tools.looking-glass', [
            'servers' => $this->lookingGlass->getAvailableServers(),
        ]);
    }

    /**
     * Query BGP routes
     */
    public function query(Request $request): JsonResponse
    {
        $request->validate([
            'resource' => 'required|string',
        ]);

        $resource = $request->input('resource');

        $result = $this->lookingGlass->getComprehensiveBgpView($resource);

        return response()->json($result);
    }

    /**
     * Get route visibility
     */
    public function visibility(Request $request): JsonResponse
    {
        $request->validate([
            'prefix' => 'required|string',
        ]);

        $result = $this->lookingGlass->getRouteVisibility($request->prefix);

        return response()->json($result);
    }

    /**
     * Get RPKI/ROA status
     */
    public function rpkiStatus(Request $request): JsonResponse
    {
        $request->validate([
            'prefix' => 'required|string',
        ]);

        $result = $this->lookingGlass->getRpkiStatus($request->prefix);

        return response()->json($result);
    }

    /**
     * Get BGP updates
     */
    public function bgpUpdates(Request $request): JsonResponse
    {
        $request->validate([
            'prefix' => 'required|string',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
        ]);

        $result = $this->lookingGlass->getBgpUpdates(
            $request->prefix,
            $request->start_time,
            $request->end_time
        );

        return response()->json($result);
    }

    /**
     * Get routing consistency
     */
    public function consistency(Request $request): JsonResponse
    {
        $request->validate([
            'prefix' => 'required|string',
        ]);

        $result = $this->lookingGlass->getRoutingConsistency($request->prefix);

        return response()->json($result);
    }

    /**
     * Check if prefix is announced
     */
    public function isAnnounced(Request $request): JsonResponse
    {
        $request->validate([
            'prefix' => 'required|string',
        ]);

        return response()->json([
            'prefix' => $request->prefix,
            'announced' => $this->lookingGlass->isPrefixAnnounced($request->prefix),
        ]);
    }
}
