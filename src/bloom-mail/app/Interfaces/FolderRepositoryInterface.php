<?php

namespace App\Interfaces;

use App\Models\Folder;
use Illuminate\Http\Request;

interface FolderRepositoryInterface
{
    public function index();

    public function getOnlyFolders();

    public function store(Request $request);

    public function update(Request $request, Folder $folder);

    public function delete(Folder $folder);
}
