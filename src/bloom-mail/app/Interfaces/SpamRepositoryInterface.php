<?php

namespace App\Interfaces;

use App\Models\Spam;
use Illuminate\Http\Request;

interface SpamRepositoryInterface
{
    public function index();

    public function store(Request $request);

    public function update(Request $request, Spam $spam);

    public function delete(Spam $spam);
}
