<?php

namespace App\Enums;

enum RoleEnum:string
{
    case Admin = 'Admin';
    case User = 'User';
    case Vendor = 'Vendor';
}
