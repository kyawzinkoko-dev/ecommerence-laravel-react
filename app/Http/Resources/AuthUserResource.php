<?php

namespace App\Http\Resources;

use App\Enums\VendorStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public static $wrap = false;
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'name' => $this->name,
            'permission' => $this->getAllPermissions()->map(function ($permission) {
                return $permission->name;
            }),
            'role_name'=>$this->getRoleNames(),
            'stripe_account_active'=>(bool) $this->stripe_account_active,
            'vendor'=>!$this->vendor ?null : [
                'status'=>$this->vendor->status, 
                'status_label'=>VendorStatusEnum::from($this->vendor->status)->label(),
                'store_name'=>$this->vendor->store_name,
                'store_address'=>$this->vendor->store_address,
                'cover_image'=>$this->vendor->cover_image,
                ] 
        ];
    }
}
