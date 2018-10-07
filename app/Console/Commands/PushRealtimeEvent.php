<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Realtime\RealtimeEvent;
use \App\User;

class PushRealtimeEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'realtime:emit
                            {--i|interactive : Use the interactive prompt}
                            {--t|type=notification : The type of realtime event to emit}
                            {--c|content= : The content of the realtime event}
                            {--u|users=* : The users to emit the event to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Emit a realtime event to a user or users.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $interactive = $this->option('interactive');
        $type = $this->option('type');
        $content = $this->option('content');
        $users = $this->option('users');
        if (true === $interactive) {
            $type = $this->anticipate('What type of realtime event are you sending?', [$type]);
            $content = $this->anticipate('What is the json encoded content of the event you are sending? (Optional)', [$content]);
            $users = array_map('trim', explode(',', $this->anticipate('What users would you like to send the event to (Comma seperated list of IDs)?', $users)));
        }
        if (!is_array($users) || count($users) < 1) {
            $this->error('You must pass at least 1 user ID to continue');
        }
        $content = json_decode($content);
        $bar = $this->output->createProgressBar(count($users));
        if ($users[0] == 'all') {
            $users = User::all();
        }
        foreach ($users as $user) {
            $sent = RealtimeEvent::emit($user, $type, $content);
            echo "\n";
            if (true == $sent) {
                $this->info('Emitted Successfully');
            } else {
                $this->error('Emission Failed');
            }
            $bar->advance();
        }
        echo "\n";
    }
}
