<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ApiError;
use App\Enums\ApiSuccess;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\Admin\VendorDetailResource;
use App\Http\Resources\TripResource;
use App\Models\Trips;
use App\Models\VendorRoute;
use App\Http\Requests\Api\Admin\CreateVendorRequest; 
use App\Services\UserService;


class VendorController extends Controller
{
    use ResponseHelper;


    public function __construct(UserService $userService) 
    {
        $this->userService = $userService;
    }

    public function store(CreateVendorRequest $request)
    {
        $validated = $request->validated();

        
        $user = $this->userService->createVendor($validated); 

        return $this->success([
            'user_id' => $user->id,
           
            'vendor_id' => $user->vendor->id, 
            'message' => 'Tạo nhà xe thành công. Chờ duyệt.',
        ], ApiSuccess::CREATED_SUCCESS);
    }


    public function index(Request $request)
    {
        $vendors = Vendor::with('user')->orderBy('company_name')->get(['id','user_id','company_name','status','logo_url']);
        return $this->success($vendors, ApiSuccess::GET_DATA_SUCCESS);
    }

    public function show(Vendor $vendor)
    {
        $vendor->load('user', 'vendorRoutes');
        return $this->success(new VendorDetailResource($vendor), ApiSuccess::GET_DATA_SUCCESS);
    }

    public function trips(int $vendor)
    {
        $routes = VendorRoute::where('vendor_id', $vendor)->pluck('id');
        $trips = Trips::with(['vendorRoute.vendor.user','coaches.vehicle'])
            ->whereIn('vendor_route_id', $routes)
            ->latest('id')
            ->limit(200)
            ->get();
        return $this->success(TripResource::collection($trips), ApiSuccess::GET_DATA_SUCCESS);
    }
    
    public function updateStatus(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['active', 'pending', 'suspended'])]
        ]);

        $vendor->status = $validated['status'];
        $vendor->save();

        return $this->success(null, ApiSuccess::ACTION_SUCCESS);
    }


    public function update(Request $request, Vendor $vendor)
    {
       
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'user_name' => 'required|string|max:255',
            'phone_number' => 'required|string|digits:10',
            'status' => ['required', 'string', Rule::in(['active', 'pending', 'suspended'])],
            'logo_url' => 'nullable|url|max:2048',
            'role' => ['required', 'string', Rule::in(['customer', 'vendor', 'admin'])], 
        ]);

        
        $vendor->update([
            'company_name' => $validated['company_name'],
            'address' => $validated['address'],
            'status' => $validated['status'],
            'logo_url' => $validated['logo_url'] ?? $vendor->logo_url,
        ]);

       
        $vendor->user->update([
            'name' => $validated['user_name'],
            'phone_number' => $validated['phone_number'],
            'role' => $validated['role'], 
        ]);

        return $this->success(null, ApiSuccess::ACTION_SUCCESS);
    }
}