<?php

namespace App\Http\Controllers\System;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationCreateRequest;
use App\Interfaces\NotificationRepositoryInterface;

class NotificationController extends Controller
{
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!check_user_permission('noti_read')) {
            return abort(401);
        }

        $notifications = $this->notificationRepository->index($request);

        return Inertia::render('System/Notification/Index', [
            "notifications" => $notifications['data']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!check_user_permission('noti_createdit')) {
            return abort(401);
        }

        return Inertia::render('System/Notification/CreateEdit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NotificationCreateRequest $request)
    {
        if (!check_user_permission('noti_createdit')) {
            return abort(401);
        }

        $createNoti = $this->notificationRepository->store($request);

        return redirect()->route('notifications.index')->with('success', 'Form submitted successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        if (!check_user_permission('noti_createdit')) {
            return abort(401);
        }

        return Inertia::render('System/Notification/CreateEdit', [
            "notification" => $notification,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NotificationCreateRequest $request, Notification $notification)
    {
        if (!check_user_permission('noti_createdit')) {
            return abort(401);
        }

        $updateNoti = $this->notificationRepository->update($request, $notification);

        return redirect()->route('notifications.index')->with('success', 'Form submitted successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        if (!check_user_permission('noti_delete')) {
            return abort(401);
        }

        $deleteNoti = $this->notificationRepository->delete($notification);

        return redirect()->back();
    }
}
