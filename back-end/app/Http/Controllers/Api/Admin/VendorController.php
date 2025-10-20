<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ApiError;
use App\Enums\ApiSuccess;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    use ResponseHelper;

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
}