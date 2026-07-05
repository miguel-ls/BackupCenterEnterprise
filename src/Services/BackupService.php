<?php

namespace BackupCenter\Services;

use BackupCenter\Core\HostedAgent;
use BackupCenter\Core\JobConfiguration;
use BackupCenter\Repositories\JobRepository;
use BackupCenter\Repositories\ConnectionRepository;

class BackupService
{
    private HostedAgent $agent;
    private JobRepository $jobRepository;
    private ConnectionRepository $connectionRepository;

    public function __construct(
        HostedAgent $agent,
        JobRepository $jobRepository,
        ConnectionRepository $connectionRepository
    ) {
        $this->agent = $agent;
        $this->jobRepository = $jobRepository;
        $this->connectionRepository = $connectionRepository;
    }

    /**
     * Ejecuta un trabajo manualmente.
     */
    public function run(int $jobId): bool
    {
        $job = $this->jobRepository->getJob($jobId);

        if (!$job) {
            return false;
        }

        $connection = null;

        if (!empty($job['connection_id'])) {
            $connection = $this->connectionRepository->get(
                (int)$job['connection_id']
            );
        }

        if (!$connection) {
            return false;
        }

$configuration = new JobConfiguration(
    $job + $connection
);

        return $this->agent->executeJob($configuration);
    }
}