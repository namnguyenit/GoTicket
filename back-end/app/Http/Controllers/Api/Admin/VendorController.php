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
use App\Http\Requests\Api\Admin\CreateVendorRequest; // ✅ THÊM DÒNG NÀY
use App\Services\UserService;


class VendorController extends Controller
{
    use ResponseHelper;


    public function __construct(UserService $userService) 
    {
        $this->userService = $userService;
    }

    /**
     * ✅ THÊM PHƯƠNG THỨC NÀY: Tạo một Vendor mới.
     */
    public function store(CreateVendorRequest $request)
    {
        $validated = $request->validated();

        // Gọi Service để tạo User (role=vendor) và Vendor trong transaction
        $user = $this->userService->createVendor($validated); 

        return $this->success([
            'user_id' => $user->id,
            // Lấy vendor ID qua mối quan hệ đã được tạo trong transaction
            'vendor_id' => $user->vendor->id, 
            'message' => 'Tạo nhà xe thành công. Chờ duyệt.',
        ], ApiSuccess::CREATED_SUCCESS);
    }


    public function show(Vendor $vendor)
    {
        
        $vendor->load('user', 'vendorRoutes');

        // Trả về thông tin chi tiết qua VendorDetailResource
        return $this->success(new VendorDetailResource($vendor), ApiSuccess::GET_DATA_SUCCESS);
    }
    /**
     * Cập nhật trạng thái của một nhà xe.
     */
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
        // 1. Validate dữ liệu công ty và user
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'user_name' => 'required|string|max:255',
            'phone_number' => 'required|string|digits:10',
            'status' => ['required', 'string', Rule::in(['active', 'pending', 'suspended'])],
            // ✅ THÊM VALIDATION CHO ROLE
            'role' => ['required', 'string', Rule::in(['customer', 'vendor', 'admin'])], 
        ]);

        // 2. Cập nhật bảng vendors
        $vendor->update([
            'company_name' => $validated['company_name'],
            'address' => $validated['address'],
            'status' => $validated['status'],
        ]);

        // 3. Cập nhật bảng users (thông tin người đại diện)
        $vendor->user->update([
            'name' => $validated['user_name'],
            'phone_number' => $validated['phone_number'],
            'role' => $validated['role'], // ✅ CẬP NHẬT ROLE
        ]);

        return $this->success(null, ApiSuccess::ACTION_SUCCESS);
    }
}