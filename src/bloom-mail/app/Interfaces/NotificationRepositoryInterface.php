<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use App\Models\Notification;
use Spatie\Permission\Models\Permission;

interface NotificationRepositoryInterface
{
    public function index(Request $request);

    public function store(Request $request);

    public function update(Request $request, Notification $notification);

    public function delete(Notification $notification);
}
