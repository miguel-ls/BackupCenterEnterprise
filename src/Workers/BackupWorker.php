<?php

namespace BackupCenter\Workers;

use BackupCenter\Repositories\JobQueueRepository;
use BackupCenter\Services\BackupService;

class BackupWorker
{
    public function __construct(
        private JobQueueRepository $queue,
        private BackupService $backupService
    ) {
    }

    public function process(): void
    {
        $item = $this->queue->getNext();

        if (!$item) {

            echo "[" . date('H:i:s') . "] Esperando trabajos..." . PHP_EOL;

            return;
        }

        $this->queue->start(
            (int)$item['id'],
            gethostname()
        );

        try {

            echo "Worker ejecutando Job {$item['job_id']}" . PHP_EOL;

            sleep(10);


            $this->backupService->run(
                (int)$item['job_id']
            );

            $this->queue->finish(
                (int)$item['id']
            );

        } catch (\Throwable $e) {

            $this->queue->fail(
                (int)$item['id'],
                $e->getMessage()
            );

            throw $e;
        }
    }
}