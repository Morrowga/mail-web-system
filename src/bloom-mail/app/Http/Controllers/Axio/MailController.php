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

    public function markAsRead(Request $request,$id)
    {
        $response = $this->mailRepository->markAsRead($id);

        return $response;
    }

    public function getHistories(Request $request,$id)
    {
        $response = $this->mailRepository->getHistories($id);

        return $response;
    }
}
