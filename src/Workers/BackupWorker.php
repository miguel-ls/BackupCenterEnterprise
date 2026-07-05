<?php

namespace BackupCenter\Workers;

use BackupCenter\Queue\JobQueue;
use BackupCenter\Services\BackupService;

class BackupWorker
{
    public function __construct(
        private JobQueue $queue,
        private BackupService $backupService
    ) {
    }

    public function process(): void
    {
        while (!$this->queue->isEmpty()) {

            $jobId = $this->queue->dequeue();

            if ($jobId === null) {
                return;
            }

            echo "Worker ejecutando Job {$jobId}" . PHP_EOL;

            $this->backupService->run($jobId);
        }
    }
}