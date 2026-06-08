<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WeeklyStipendReportMail extends Mailable
{
    public $fileName;
    public $start;
    public $end;

    public function __construct($fileName, $start, $end)
    {
        $this->fileName = $fileName;
        $this->start = $start;
        $this->end = $end;
    }

    // ✅ Subject
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Weekly Stipend Report - URZ',
        );
    }

    // ✅ View (REPLACES build()->view())
    public function content(): Content
    {
        return new Content(
            view: 'admin.emails.weekly_stipend',
            with: [
                'start' => $this->start,
                'end' => $this->end,
            ]
        );
    }

    // ✅ Attach file
    public function attachments(): array
    {
        return [
            \Illuminate\Mail\Mailables\Attachment::fromPath(
                storage_path('app/public/' . $this->fileName)
            )
        ];
    }
}