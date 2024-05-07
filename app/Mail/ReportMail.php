<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportMail extends Mailable
{
    /**
     * Elements de report
     * @var array
     */
    public $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))->view('emails.report')->with(['report' => $this->report])->subject('Ecogest : vous avez un nouveau signalement');
    }
}
