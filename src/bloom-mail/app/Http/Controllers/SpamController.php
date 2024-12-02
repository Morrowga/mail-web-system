<?php

namespace App\Http\Controllers;

use App\Models\Spam;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\SpamRequest;
use App\Interfaces\MailRepositoryInterface;
use App\Interfaces\SpamRepositoryInterface;

class SpamController extends Controller
{
    private SpamRepositoryInterface $spamRepository;

    public function __construct(SpamRepositoryInterface $spamRepository)
    {
        $this->spamRepository = $spamRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!check_user_permission('spam_read')) {
            return abort(401);
        }

        $spams = $this->spamRepository->index();

        return Inertia::render('Mail/Spams/Index', [
            "spams" => $spams['data']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!check_user_permission('spam_createdit')) {
            return abort(401);
        }

        return Inertia::render('Mail/Spams/CreateEdit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SpamRequest $request)
    {
        if (!check_user_permission('spam_createdit')) {
            return abort(401);
        }

        $createSpam = $this->spamRepository->store($request);

        return redirect()->route('spams.index')->with('success', 'Form submitted successfully');
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
    public function edit(Spam $spam)
    {
        if (!check_user_permission('spam_createdit')) {
            return abort(401);
        }

        return Inertia::render('Mail/Spams/CreateEdit', [
            "spam" => $spam
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SpamRequest $request, Spam $spam)
    {
        if (!check_user_permission('spam_createdit')) {
            return abort(401);
        }

        $updateSpam = $this->spamRepository->update($request, $spam);

        return redirect()->route('spams.index')->with('success', 'Form submitted successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Spam $spam)
    {
        if (!check_user_permission('spam_delete')) {
            return abort(401);
        }

        $deleteSpam = $this->spamRepository->delete($spam);

        return redirect()->back();
    }
}
