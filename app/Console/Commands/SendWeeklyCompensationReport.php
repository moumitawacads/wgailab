<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyCompensationReportMail;

class SendWeeklyCompensationReport extends Command
{
    protected $signature = 'report:weekly-compensation';
    protected $description = 'Send weekly compensation requests to admin';

    public function handle()
    {
        // Get last weekend (Saturday → Sunday)
        $start = Carbon::now()->previous(Carbon::SATURDAY)->toDateString();
        $end   = Carbon::now()->previous(Carbon::SUNDAY)->toDateString();

       // $this->info($start.$end);

        // Fetch compensation requests
        $requests = DB::table('compensation_requests')
            ->join('users', 'users.id', '=', 'compensation_requests.user_id')
            ->join('weekly_stipend_reports', 'weekly_stipend_reports.id', '=', 'compensation_requests.report_id')
            ->select(
                'compensation_requests.*',
                'users.name as user_name',
                'users.email',
                'weekly_stipend_reports.week_start',
                'weekly_stipend_reports.week_end'
            )
            ->whereBetween(DB::raw('DATE(compensation_requests.created_at)'), [$start, $end])
            ->get();

        if ($requests->isEmpty()) {
            $this->info('No compensation requests found for weekend.');
            return;
        }

        // Admin emails (can be from settings table also)
        $adminEmails = explode(',',admin_settings('compensation_list_email'));

        // ✅ Send mail
        Mail::to($adminEmails)->send(
            new WeeklyCompensationReportMail([
                'requests' => $requests,
                'start' => $start,
                'end' => $end
            ])
        );

        $this->info('Weekly compensation report sent successfully.');
    }
}