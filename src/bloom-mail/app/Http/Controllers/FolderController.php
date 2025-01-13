<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Folder;
use Illuminate\Http\Request;
use App\Http\Requests\FolderRequest;
use App\Interfaces\FolderRepositoryInterface;

class FolderController extends Controller
{
    private FolderRepositoryInterface $folderRepository;

    public function __construct(FolderRepositoryInterface $folderRepository)
    {
        $this->folderRepository = $folderRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!check_user_permission('folder_read')) {
            return abort(401);
        }

        $folders = $this->folderRepository->index();

        return Inertia::render('Folders/Index', [
            "folders" => $folders['data']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!check_user_permission('folder_createdit')) {
            return abort(401);
        }

        return Inertia::render('Folders/CreateEdit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FolderRequest $request)
    {
        if (!check_user_permission('folder_createdit')) {
            return abort(401);
        }

        $createFolder = $this->folderRepository->store($request);

        return redirect()->route('folders.index')->with('success', 'Form submitted successfully');
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
    public function edit(Folder $folder)
    {
        if (!check_user_permission('folder_createdit')) {
            return abort(401);
        }

        $folder->load('extra_searches');

        return Inertia::render('Folders/CreateEdit', [
            "folder" => $folder
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FolderRequest $request, Folder $folder)
    {
        if (!check_user_permission('folder_createdit')) {
            return abort(401);
        }

        $updateFolder = $this->folderRepository->update($request, $folder);

        return redirect()->route('folders.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Folder $folder)
    {
        if (!check_user_permission('folder_delete')) {
            return abort(401);
        }

        $deleteFolder = $this->folderRepository->delete($folder);

        return redirect()->back();
    }
}
