<?php

namespace BackupCenter\Services;

use BackupCenter\Repositories\JobRepository;
use Cron\CronExpression;

class SchedulerService
{
    private JobRepository $repository;
    private BackupService $backupService;

    public function __construct(
        JobRepository $repository,
        BackupService $backupService
    ) {
        $this->repository = $repository;
        $this->backupService = $backupService;
    }

public function run(bool $force = false): void
{
    $jobs = $this->repository->getEnabledJobs();

    foreach ($jobs as $job) {

        echo "Evaluando trabajo: {$job['name']}" . PHP_EOL;

        $cron = new CronExpression(
            $job['schedule']
        );

if (!$force && !$cron->isDue()) {

    echo "No corresponde ejecutar." . PHP_EOL;

    continue;
}

        echo "Ejecutando..." . PHP_EOL;

        $this->backupService->run(
            (int)$job['id']
        );
    }
}
}