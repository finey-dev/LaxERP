<?php

namespace App\Console\Commands;

use App\Notifications\FollowUpReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Workdo\Lead\Entities\Lead;

class SendFollowUpReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-follow-up-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();

        // Fetch all follow-ups that are due for a reminder
        $followUps = Lead::where(function ($query) use ($now) {
            $query->where('follow_up_date', '=', $now->addWeek())
                ->where('follow_up_date', '=', $now->addDay())
                ->where('follow_up_date', '=', $now->addHour());
        })->get();

        foreach ($followUps as $followUp) {
            $user = $followUp->creator; // Assuming there's a relationship with the user who created it
            $user->notify(new FollowUpReminder($followUp));
        }

        $this->info('Follow-up reminders sent successfully.');
    }
}
