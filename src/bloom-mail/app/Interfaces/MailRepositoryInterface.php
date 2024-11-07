<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface MailRepositoryInterface
{
    public function inbox();

    public function store(Request $request);
}
