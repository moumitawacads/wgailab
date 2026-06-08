<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Notification;
use App\Mail\NonQualifiedStipendMail;
use Illuminate\Support\Facades\Log;

class GenerateWeeklyStipend extends Command
{
    protected $signature = 'report:generate-weekly-stipend';
    protected $description = 'Generate weekly stipend report';

    public function handle()
    {
        //Log::info('Called Weekly Stipend Report');
        $service = app(AttendanceService::class);

        /*
        // Last week (Mon → Sun)
        $startOfWeek = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endOfWeek   = Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY)->toDateString();
        */

        // Ongoing week (Mon → Sun)
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endOfWeek   = Carbon::now()->endOfWeek(Carbon::SUNDAY)->toDateString();

        // STEP 1: STOP if already generated
        $alreadyGenerated = DB::table('weekly_stipend_reports')
            ->where('week_start', $startOfWeek)
            ->where('week_end', $endOfWeek)
            ->where('generation_status', 1)
            ->exists();

        if ($alreadyGenerated) {
            $this->info('⚠️ Weekly stipend already generated for this week.');
            return;
        }
        

        //STEP 2: Fetch data
        // $data = $service->getWeeklyAttendanceQuery($startOfWeek, $endOfWeek)
        //     ->whereBetween('users_classes_mappings.schedule_date', [
        //         $startOfWeek,
        //         $endOfWeek
        //     ])
        //     ->get();

        $data=$service->getWeeklyAttendanceQuery($startOfWeek, $endOfWeek)->get();
        $lowAttendanceUsers = [];

           // $this->info(json_encode($data));

            // STEP :: Process records
       foreach ($data as $row) {

            $percentage = ($row->total_classes > 0)
                ? ($row->present_count / $row->total_classes) * 100
                : 0;
            if ($percentage < 80) {
                $lowAttendanceUsers[] = [
                    'name' => $row->user_name,
                    'email'=> $row->user_email,
                    'phone'=> $row->user_phone,
                    'percentage' => round($percentage, 2),
                ];
            }    

            $stipend = ($percentage == 100)
                ? admin_settings('stipend_amount')
                : 0;

            // ✅ Insert record
            DB::table('weekly_stipend_reports')->insert([
                'user_id' => $row->user_id,
                'week_start' => $startOfWeek,
                'week_end' => $endOfWeek,
                'total_classes' => $row->total_classes,
                'present_count' => $row->present_count,
                'attendance_percentage' => $percentage,
                'stipend_amount' => $stipend,
                'settled_stipend_amount' => $stipend,
                'generation_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Notification::create([
                'title' => 'Stipend Update',
                'message' => 'Your stipend for this week has been successfully calculated.',
                'user_id' => $row->user_id,
                'type' => 'success',
                'icon' => 'bell',
                'is_read' => 0,
            ]);

            // ✅ 🔥 SEND MAIL if < 90%
            if ($percentage < 90) {

                $user = User::find($row->user_id);

                if ($user && $user->email) {

                    Notification::create([
                        'title' => 'Low Attendance',
                        'message' => 'Your attendance for this week is below the required threshold, so a stipend cannot be processed for this period.',
                        'user_id' => $row->user_id,
                        'type' => 'danger',
                        'icon' => 'alert-circle',
                        'is_read' => 0,
                    ]);

                    Mail::to($user->email)->send(
                        new NonQualifiedStipendMail([
                            'name' => $user->name,
                            'percentage' => round($percentage, 2),
                            'week_start' => $startOfWeek,
                            'week_end' => $endOfWeek,
                        ])
                    );
                }
            }
        }

        $adminEmails = admin_settings('compensation_list_email');

        $emailArray = array_map('trim', explode(',', $adminEmails));

        if (!empty($lowAttendanceUsers)) {

            Mail::send(
                'admin.emails.low_attendance_summary',
                ['users' => $lowAttendanceUsers],
                function ($message) use ($emailArray) {

                    $message->to($emailArray)
                        ->subject('Users Below 80% Attendance');

                }
            );
        }
        $this->info('Weekly stipend report generated successfully.');
    }
}