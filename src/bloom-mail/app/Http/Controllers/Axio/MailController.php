<?php

namespace App\Http\Controllers\Axio;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\MailRepositoryInterface;

class MailController extends Controller
{
    private MailRepositoryInterface $mailRepository;

    public function __construct(MailRepositoryInterface $mailRepository)
    {
        $this->mailRepository = $mailRepository;
    }

    public function index()
    {
        $mails = $this->mailRepository->inbox();

        return response()->json($mails);
    }

    public function markAsRead(Request $request,$uid)
    {
        $response = $this->mailRepository->markAsRead($uid);

        return $response;
    }
}
