<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\TenantResource;
use App\Models\Tenant\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Display all tenants for the current user.
     */
    public function index(Request $request): JsonResponse
    {
        $tenants = $request->user()->tenants;

        $arr = TenantResource::collection($tenants);

        return response()->json($arr);
    }

    /**
     * Display tenant info.
     */
    public function show(Tenant $tenant): JsonResponse
    {
        return response()->json(new TenantResource($tenant));
    }
}
