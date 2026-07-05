<?php

namespace BackupCenter\Services;

use BackupCenter\Repositories\JobRepository;

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

    public function run(): void
    {
        $jobs = $this->repository->getEnabledJobs();

foreach ($jobs as $job) {

    echo "Evaluando trabajo: {$job['name']}" . PHP_EOL;

    $this->backupService->run(
        (int)$job['id']
    );
}
    }
}