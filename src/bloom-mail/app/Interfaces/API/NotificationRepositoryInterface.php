<?php

namespace App\Interfaces\API;

use Illuminate\Http\Request;
use App\Models\Notification;
use Spatie\Permission\Models\Permission;

interface NotificationRepositoryInterface
{
    public function index(Request $request);
}
