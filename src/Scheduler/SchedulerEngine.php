<?php

namespace BackupCenter\Scheduler;

use BackupCenter\Core\Audit;
use BackupCenter\Repositories\JobRepository;
use BackupCenter\Repositories\JobQueueRepository;

class SchedulerEngine
{
    public function __construct(
        private JobRepository $repository,
        private CronEvaluator $cron,
        private JobRunner $runner,
        private JobQueueRepository $queue
    ) {
    }

    public function execute(bool $force = false): void
    {
        $jobs = $this->repository->getEnabledJobs();

        Audit::info(

            "SCHEDULER",

            "START",

            "Scheduler iniciado.",

            "SYSTEM"

        );

        foreach ($jobs as $job) {

            echo "Evaluando: {$job['name']}" . PHP_EOL;

            if ($this->repository->isRunning((int)$job['id'])) {

                Audit::info(

                    "SCHEDULER",

                    "SKIP",

                    "Job {$job['name']} ya está ejecutándose.",

                    "SYSTEM"

                );

                continue;
            }

            if (
                !$force &&
                $this->repository->executedThisMinute((int)$job['id'])
            ) {

                Audit::info(

                    "SCHEDULER",

                    "SKIP",

                    "Job {$job['name']} ya fue ejecutado este minuto.",

                    "SYSTEM"

                );

                continue;
            }

            if (
                !$force &&
                !$this->cron->isDue($job['schedule'])
            ) {

                continue;
            }

            $this->queue->enqueue(
                (int)$job['id']
            );

            Audit::info(

                "SCHEDULER",

                "QUEUE",

                "Job {$job['name']} agregado a la cola.",

                "SYSTEM"

            );

            echo "Encolando Job {$job['id']}" . PHP_EOL;
        }

        Audit::info(

            "SCHEDULER",

            "END",

            "Scheduler finalizado.",

            "SYSTEM"

        );
    }
}