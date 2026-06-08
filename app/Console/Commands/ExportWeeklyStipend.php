<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\WeeklyStipendReport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WeeklyStipendExport;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyStipendReportMail;

class ExportWeeklyStipend extends Command
{
    protected $signature = 'report:export-weekly-stipend';
    protected $description = 'Export weekly stipend report from DB and send email';

    public function handle()
    {
        // ✅ Last week (Mon-Sun)
        $startOfWeek = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endOfWeek   = Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY)->toDateString();
        //$this->info($startOfWeek.$endOfWeek);
        // ✅ Fetch stored data
        $data = WeeklyStipendReport::with('user')
            ->where('week_start', $startOfWeek)
            ->where('week_end', $endOfWeek)
            ->where('generation_status', 1)
            ->orderBy('user_id')
            ->get();

        if ($data->isEmpty()) {
            $this->info('No stipend data found for last week.');
            return;
        }

        // ✅ File name
        $fileName = "weekly-stipend-{$startOfWeek}.xlsx";

        // ✅ Store Excel
        Excel::store(new WeeklyStipendExport($data), $fileName, 'public');

        $adminEmails = explode(',',admin_settings('stipend_pay_out_emails'));

        // ✅ (Optional) Send Email
        Mail::to($adminEmails) // change emails
            ->send(new WeeklyStipendReportMail($fileName, $startOfWeek, $endOfWeek));

        $this->info('✅ Weekly stipend Excel generated & emailed for '.$startOfWeek.' - '.$endOfWeek);
    }
}