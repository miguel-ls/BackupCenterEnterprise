<?php

namespace BackupCenter\Core;

class HostedAgent
{
    private Application $application;
    private Scheduler $scheduler;

    public function __construct(
        Application $application,
        Scheduler $scheduler
    ) {
        $this->application = $application;
        $this->scheduler = $scheduler;
    }

    public function executeOnce(): void
    {
        $this->application
            ->agent()
            ->run();
    }   
        
    public function run(): void
    {
        $interval = $this->application
            ->config()
            ->get('scheduler.interval_seconds');

        $this->scheduler->run(

            function () {

                $this->application
                    ->agent()
                    ->run();

            },

            $interval
        );
    }

 
}