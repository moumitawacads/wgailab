<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;

class SettingsController extends Controller
{
    public function getsettings()
    {
        $title = 'Admin ';
        $settings = Settings::get();
        return view('admin.settings', compact('title', 'settings'));
    }


    public function saveSettings(Request $request)
    {
        $request->validate([
            'admin_email' => 'required|email',
            'week_start_day' => 'required',
            // 'week_end_day'=>'required',
            'stipend_amount' => 'required',
            'compensation_list_email' => 'required',
            'stipend_pay_out_emails' => 'required',
            'media_url' => 'nullable'
        ]);
        $data = [
            'admin_email' => $request->admin_email,
            'week_start_day' => $request->week_start_day,
            // 'week_end_day' => $request->week_end_day,
            'stipend_amount' => $request->stipend_amount,
            'compensation_list_email' => $request->compensation_list_email,
            'stipend_pay_out_emails' => $request->stipend_pay_out_emails,
            'media_url' => $request->media_url,
        ];

        foreach ($data as $key => $value) {
            Settings::updateOrCreate(
                ['option_name' => $key],
                ['option_value' => $value]
            );
        }

        return back()->with('success', 'Settings saved successfully!');
    }
}
