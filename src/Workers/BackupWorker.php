<?php

namespace BackupCenter\Workers;

use BackupCenter\Repositories\JobQueueRepository;
use BackupCenter\Repositories\NotificationRepository;
use BackupCenter\Services\BackupService;

class BackupWorker
{
    public function __construct(
        private JobQueueRepository $queue,
        private BackupService $backupService,
        private NotificationRepository $notifications
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

            $this->backupService->run(
                (int)$item['job_id']
            );

            $this->queue->finish(
                (int)$item['id']
            );

            $this->notifications->add(
                'INFO',
                'Backup completado',
                "El Job {$item['job_id']} finalizó correctamente."
            );

        } catch (\Throwable $e) {

            $this->queue->fail(
                (int)$item['id'],
                $e->getMessage()
            );

            $this->notifications->add(
                'ERROR',
                'Backup fallido',
                "El Job {$item['job_id']} falló: {$e->getMessage()}"
            );

            throw $e;
        }
    }
}