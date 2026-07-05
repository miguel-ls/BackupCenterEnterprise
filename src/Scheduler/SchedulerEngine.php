<?php

namespace BackupCenter\Scheduler;

use BackupCenter\Repositories\JobRepository;
use BackupCenter\Queue\JobQueue;

class SchedulerEngine
{
    public function __construct(
        private JobRepository $repository,
        private CronEvaluator $cron,
        private JobRunner $runner,
        private JobQueue $queue
    ) {
    }

    public function execute(bool $force = false): void
    {
        $jobs = $this->repository->getEnabledJobs();

        foreach ($jobs as $job) {

            echo "Evaluando: {$job['name']}" . PHP_EOL;

            if ($this->repository->isRunning((int)$job['id'])) {

                echo "Ya está ejecutándose." . PHP_EOL;
                continue;
            }

            if (!$force && !$this->cron->isDue($job['schedule'])) {

                echo "No corresponde ejecutar." . PHP_EOL;
                continue;
            }

            echo "Encolando Job {$job['id']}" . PHP_EOL;

            $this->queue->enqueue(
                (int)$job['id']
            );
        }
    }
}