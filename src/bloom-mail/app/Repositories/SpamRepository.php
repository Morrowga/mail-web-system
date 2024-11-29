<?php

namespace App\Repositories;

use App\Models\Spam;
use App\Models\MailLog;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use App\Repositories\MailRepository;
use App\Interfaces\SpamRepositoryInterface;

class SpamRepository implements SpamRepositoryInterface
{
    use CRUDResponses;

    public function index()
    {
        try {

            $spams = Spam::get();

            return $this->success('Fetched Spams', $spams);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $spam = Spam::create($request->all());

            $mailLogs = MailLog::where('sender', $spam->mail_address)->get();

            $mailRepository = app(MailRepository::class);

            foreach($mailLogs as $mailLog)
            {
                $mailRepository->softDelete($mailLog);
            }

            DB::commit();

            return $this->success('Spam has been created successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request, Spam $spam)
    {
        DB::beginTransaction();

        try {
            if($spam)
            {
                $spam->update($request->all());

                $mailLogs = MailLog::where('sender', $spam->mail_address)->get();

                $mailRepository = app(MailRepository::class);

                foreach($mailLogs as $mailLog)
                {
                    $mailRepository->softDelete($mailLog);
                }

                DB::commit();

                return $this->success('Spam has been updated successfully.');

            }

            return $this->error('Data not found');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function delete(Spam $spam)
    {
        try {
            if($spam)
            {
                $mailLogs = MailLog::where('sender', $spam->mail_address)->get();

                $mailRepository = app(MailRepository::class);

                foreach($mailLogs as $mailLog)
                {
                    $mailRepository->redoProcess($mailLog);
                }

                $spam->delete();
            }

            return $this->success('Spam has been deleted');

        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
