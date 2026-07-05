<?php

namespace BackupCenter\Scheduler;

use BackupCenter\Repositories\JobRepository;

class SchedulerEngine
{
    public function __construct(
        private JobRepository $repository,
        private CronEvaluator $cron,
        private JobRunner $runner
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

            $this->repository->setRunning(
                (int)$job['id'],
                true
            );

            try {

                $this->runner->run(
                    (int)$job['id']
                );

            } finally {

                $this->repository->setRunning(
                    (int)$job['id'],
                    false
                );
            }
        }
    }
}