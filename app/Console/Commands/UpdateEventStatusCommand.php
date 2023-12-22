<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;

class UpdateEventStatusCommand extends Command
{
    // /**
    //  * The name and signature of the console command.
    //  *
    //  * @var string
    //  */
    // protected $signature = 'app:update-event-status-command';

    // /**
    //  * The console command description.
    //  *
    //  * @var string
    //  */
    // protected $description = 'Command description';

    // /**
    //  * Execute the console command.
    //  */
    // public function handle()
    // {
    //     //
    // }
    protected $signature = 'events:update-status';
    protected $description = 'Update event status based on date registration deadline';

    public function handle()
    {
        $eventsToUpdate = Event::where('date_reg_deadline', '<', now())
            ->where('event_status', 1) // Filter events that are still active
            ->get();

        foreach ($eventsToUpdate as $event) {
            $event->update(['event_status' => 2]); // Update the status to 2 (expired)
        }

        $this->info('Event statuses updated based on registration deadline.');
    }
}
