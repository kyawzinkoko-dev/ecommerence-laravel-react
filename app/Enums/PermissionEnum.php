<?php

namespace App\Enums;

enum PermissionEnum :string
{
    case ApprovedVendors = 'ApprovedVendors';
    case SellProducts = 'SellProducts';
    case BuyProducts = 'BuyProducts';
}
