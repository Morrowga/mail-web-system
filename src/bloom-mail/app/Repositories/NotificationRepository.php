<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use App\Interfaces\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    use CRUDResponses;

    public function index(Request $request)
    {
        try {

            $notifications = Notification::orderBy('created_at', 'desc')->paginate($request->per_page ?? 10);

            return $this->success('Fetched Notifications', $notifications);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $notification = Notification::create($request->all());

            DB::commit();

            return $this->success('Notification has been created successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request, Notification $notification)
    {
        DB::beginTransaction();

        try {
            if($notification)
            {
                $notification->update($request->all());

                DB::commit();

                return $this->success('Notification has been updated successfully.');

            }

            return $this->error('Data not found');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function delete(Notification $notification)
    {
        try {
            if($notification)
            {
                $notification->delete();
            }

            return $this->success('Notification has been deleted');

        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
