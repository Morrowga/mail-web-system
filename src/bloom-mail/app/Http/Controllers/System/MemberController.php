<?php

namespace App\Http\Controllers\System;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\MemberRepositoryInterface;

class MemberController extends Controller
{
    private MemberRepositoryInterface $memberRepository;

    public function __construct(MemberRepositoryInterface $memberRepository)
    {
        $this->memberRepository = $memberRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!check_user_permission('member_read')) {
            return abort(401);
        }

        $members = $this->memberRepository->index($request);

        return Inertia::render('System/Member/Index', [
            "members" => $members['data']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!check_user_permission('member_createdit')) {
            return abort(401);
        }

        return Inertia::render('System/Member/CreateEdit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!check_user_permission('member_createdit')) {
            return abort(401);
        }

        $createMember = $this->memberRepository->store($request);

        return redirect()->route('members.index')->with('success', 'Form submitted successfully');
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
    public function edit(User $user)
    {
        $user->load('member');

        if (!check_user_permission('member_createdit')) {
            return abort(401);
        }

        return Inertia::render('System/Member/CreateEdit', [
            "user" => $user,
            "member" => $user->member
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!check_user_permission('member_createdit')) {
            return abort(401);
        }

        $updateMember = $this->memberRepository->update($request, $user);

        return redirect()->route('members.index')->with('success', 'Form submitted successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!check_user_permission('member_delete')) {
            return abort(401);
        }

        $deleteMember = $this->memberRepository->delete($user);

        return redirect()->back();
    }
}
