<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface MailRepositoryInterface
{
    public function inbox();

    public function newMessage();

    public function markAsRead($uid);

    public function store(Request $request);
}
