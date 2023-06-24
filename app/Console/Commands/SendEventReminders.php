<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends notifications to all event atttendes that event starts soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $events = Event::with('attendees.user')->whereBetween('start_time', [now(), now()->addDay()])->get();

        $eventsCount = $events->count();

        $eventLable  = Str::plural('event', $eventsCount);
        $this->info("Found {$eventsCount} {$eventLable}");
        $events->each(function ($event) {
            $event->attendees->each(function ($attendee) {
                $this->info("Notifying the user {$attendee->user->id}");
            });
        });

        $this->info('Reminder notification sent successfully');
    }
}
