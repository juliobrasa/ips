<?php

namespace App\Http\Controllers;

use App\Services\IpToolsService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ToolsController extends Controller
{
    public function __construct(protected IpToolsService $ipTools)
    {
    }

    /**
     * Show IP tools page
     */
    public function index(): View
    {
        return view('tools.index');
    }

    /**
     * Subnet calculator
     */
    public function subnetCalculator(Request $request): View|JsonResponse
    {
        $result = null;

        if ($request->has('cidr')) {
            $request->validate([
                'cidr' => 'required|string|regex:/^(\d{1,3}\.){3}\d{1,3}\/\d{1,2}$/',
            ]);

            $validation = $this->ipTools->validateCidr($request->cidr);

            if ($validation['valid']) {
                $result = $this->ipTools->calculateSubnet($request->cidr);
            } else {
                $result = ['error' => implode(', ', $validation['errors'])];
            }
        }

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return view('tools.subnet-calculator', ['result' => $result]);
    }

    /**
     * Split subnet
     */
    public function splitSubnet(Request $request): View|JsonResponse
    {
        $result = null;

        if ($request->has('cidr') && $request->has('new_prefix')) {
            $request->validate([
                'cidr' => 'required|string|regex:/^(\d{1,3}\.){3}\d{1,3}\/\d{1,2}$/',
                'new_prefix' => 'required|integer|min:1|max:32',
            ]);

            try {
                $result = $this->ipTools->splitSubnet($request->cidr, (int) $request->new_prefix);
            } catch (\Exception $e) {
                $result = ['error' => $e->getMessage()];
            }
        }

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return view('tools.split-subnet', ['result' => $result]);
    }

    /**
     * Merge subnets
     */
    public function mergeSubnets(Request $request): View|JsonResponse
    {
        $result = null;

        if ($request->has('cidrs')) {
            $request->validate([
                'cidrs' => 'required|string',
            ]);

            $cidrs = array_filter(array_map('trim', explode("\n", $request->cidrs)));

            try {
                $merged = $this->ipTools->mergeSubnets($cidrs);
                $result = $merged ? ['merged' => $merged] : ['error' => 'Cannot merge these subnets'];
            } catch (\Exception $e) {
                $result = ['error' => $e->getMessage()];
            }
        }

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return view('tools.merge-subnets', ['result' => $result]);
    }

    /**
     * Range to CIDR converter
     */
    public function rangeToCidr(Request $request): View|JsonResponse
    {
        $result = null;

        if ($request->has('start_ip') && $request->has('end_ip')) {
            $request->validate([
                'start_ip' => 'required|ip',
                'end_ip' => 'required|ip',
            ]);

            try {
                $result = $this->ipTools->rangeToCidr($request->start_ip, $request->end_ip);
            } catch (\Exception $e) {
                $result = ['error' => $e->getMessage()];
            }
        }

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return view('tools.range-to-cidr', ['result' => $result]);
    }

    /**
     * CIDR to range converter
     */
    public function cidrToRange(Request $request): View|JsonResponse
    {
        $result = null;

        if ($request->has('cidr')) {
            $request->validate([
                'cidr' => 'required|string|regex:/^(\d{1,3}\.){3}\d{1,3}\/\d{1,2}$/',
            ]);

            try {
                $result = $this->ipTools->cidrToRange($request->cidr);
            } catch (\Exception $e) {
                $result = ['error' => $e->getMessage()];
            }
        }

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return view('tools.cidr-to-range', ['result' => $result]);
    }

    /**
     * IP in subnet checker
     */
    public function ipInSubnet(Request $request): View|JsonResponse
    {
        $result = null;

        if ($request->has('ip') && $request->has('cidr')) {
            $request->validate([
                'ip' => 'required|ip',
                'cidr' => 'required|string|regex:/^(\d{1,3}\.){3}\d{1,3}\/\d{1,2}$/',
            ]);

            $result = [
                'ip' => $request->ip,
                'cidr' => $request->cidr,
                'in_subnet' => $this->ipTools->isIpInSubnet($request->ip, $request->cidr),
            ];
        }

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return view('tools.ip-in-subnet', ['result' => $result]);
    }

    /**
     * Geofeed generator
     */
    public function geofeedGenerator(Request $request): View|JsonResponse
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'entries' => 'required|array',
                'entries.*.prefix' => 'required|string',
                'entries.*.country' => 'required|string|size:2',
                'entries.*.region' => 'nullable|string',
                'entries.*.city' => 'nullable|string',
            ]);

            $geofeed = $this->ipTools->generateGeofeed($request->entries);

            if ($request->wantsJson()) {
                return response()->json(['geofeed' => $geofeed]);
            }

            return response($geofeed, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="geofeed.csv"',
            ]);
        }

        return view('tools.geofeed-generator');
    }

    /**
     * Subnets summary
     */
    public function subnetsSummary(Request $request): View|JsonResponse
    {
        $result = null;

        if ($request->has('cidrs')) {
            $request->validate([
                'cidrs' => 'required|string',
            ]);

            $cidrs = array_filter(array_map('trim', explode("\n", $request->cidrs)));

            try {
                $result = $this->ipTools->summarizeSubnets($cidrs);
            } catch (\Exception $e) {
                $result = ['error' => $e->getMessage()];
            }
        }

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return view('tools.subnets-summary', ['result' => $result]);
    }

    /**
     * Validate CIDR
     */
    public function validateCidr(Request $request): JsonResponse
    {
        $request->validate([
            'cidr' => 'required|string',
        ]);

        return response()->json($this->ipTools->validateCidr($request->cidr));
    }

    /**
     * Get IP info
     */
    public function ipInfo(Request $request): View|JsonResponse
    {
        $result = null;

        if ($request->has('ip')) {
            $request->validate([
                'ip' => 'required|ip',
            ]);

            $ip = $request->ip;

            $result = [
                'ip' => $ip,
                'class' => $this->ipTools->getIpClass($ip),
                'is_private' => $this->ipTools->isPrivateIp($ip),
                'is_reserved' => $this->ipTools->isReservedIp($ip),
                'rir' => $this->ipTools->getRir($ip),
                'binary' => sprintf('%032b', ip2long($ip)),
                'decimal' => ip2long($ip),
                'hex' => dechex(ip2long($ip)),
            ];
        }

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return view('tools.ip-info', ['result' => $result]);
    }
}
