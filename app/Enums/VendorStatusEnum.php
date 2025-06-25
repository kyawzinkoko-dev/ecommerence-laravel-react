<?php

namespace App\Enums;

enum VendorStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function labels(){
        return [
            self::PENDING->value => __('Pending'),
            self::APPROVED->value => __('Approved'),
            self::REJECTED->value => __('Rejected'),
        ];
    }
    public function label()
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::APPROVED => __('Approved'),
            self::REJECTED => __('Rejected'),
        };
    }

}
