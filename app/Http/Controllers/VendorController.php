<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Enums\VendorStatusEnum;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    public function profile(){

    }
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'store_name' => ['required',
            'regex:/^[a-z0-9]+$/',
            Rule::unique('vendors','store_name')->ignore($user->id,'user_id')],
            'store_address'=>['nullable']
        ],[
            'store_name.regex: Store name must only contain lowercase alphanumeric characteristics and dash '
        ]);
        $vendor = $request->vendor ?: new Vendor();
        $vendor->user_id = $user->id;
        $vendor->status = VendorStatusEnum::APPROVED->value;
        $vendor->store_name = $request->store_name;
        $vendor->store_address = $request->store_address;
        $vendor->save();
        $user->assignRole(RoleEnum::Vendor);
    }
    
}
