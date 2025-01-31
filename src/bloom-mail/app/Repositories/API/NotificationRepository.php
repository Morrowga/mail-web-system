<?php

namespace App\Repositories\API;

use App\Models\Notification;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\NotificationResource;
use App\Interfaces\API\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    use ApiResponses;

    public function index(Request $request)
    {
        try {

            $notifications = Notification::orderBy('created_at', 'desc')
            ->where('status', '!=', 'draft')
            ->when($request->query('type'), function ($query, $type) {
                return $query->where('type', $type);
            })
            ->whereRaw('? BETWEEN start_time AND end_time', [now()])
            ->paginate($request->query('per_page') ?? 10);

            $notificationsArray = [
                'current_page' => $notifications->currentPage(),
                'data' => NotificationResource::collection($notifications),
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'last_page' => $notifications->lastPage(),
                'from' => $notifications->firstItem(),
                'to' => $notifications->lastItem(),
            ];

            return $this->success('Fetched Notifications', $notificationsArray);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }

}
