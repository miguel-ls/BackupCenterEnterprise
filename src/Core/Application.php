<?php

namespace BackupCenter\Core;

use BackupCenter\Builders\ScriptBuilder;
use BackupCenter\Core\WinScpProvider;
use BackupCenter\Core\RetryPolicy;
use BackupCenter\Services\UploadManager;
use BackupCenter\Core\FileScanner;
use BackupCenter\Core\FileValidator;
use BackupCenter\Repositories\UploadedFileRepository;
use BackupCenter\Repositories\ExecutionHistoryRepository;

use BackupCenter\Repositories\JobRepository;
use BackupCenter\Repositories\ConnectionRepository;
use BackupCenter\Services\BackupService;
use BackupCenter\Services\SchedulerService;

use BackupCenter\Scheduler\CronEvaluator;
use BackupCenter\Scheduler\JobRunner;
use BackupCenter\Scheduler\SchedulerEngine;
use BackupCenter\Scheduler\SchedulerLoop;

class Application
{
    private ConfigurationManager $config;
    private Logger $logger;
    private ScriptBuilder $scriptBuilder;
    private RetryPolicy $retryPolicy;
    private WinScpProvider $provider;
    private UploadManager $uploadManager;
    private FileScanner $scanner;
    private FileValidator $validator;
    private UploadedFileRepository $repository;
    private ExecutionHistoryRepository $executionHistoryRepository;
    private BackupCenterAgent $agent;
    private Database $database;

private JobRepository $jobRepository;
private ConnectionRepository $connectionRepository;
private BackupService $backupService;    

private SchedulerLoop $schedulerLoop;
private SchedulerEngine $schedulerEngine;

    public function __construct()
    {
        $this->database = new Database(
            Paths::database() . '/backupcenter.db'
        );

        $this->config = new ConfigurationManager(
            Paths::config() . '/config.json'
        );

        $this->logger = new Logger(
            Paths::logs()
        );

        $this->scriptBuilder = new ScriptBuilder();

        $this->retryPolicy = new RetryPolicy(
            $this->config->get('sftp.retry_attempts'),
            $this->config->get('sftp.retry_delay')
        );

        $this->provider = new WinScpProvider();

        $this->uploadManager = new UploadManager(
            $this->config,
            $this->provider,
            $this->scriptBuilder,
            $this->retryPolicy
        );

        $this->scanner = new FileScanner();

        $this->validator = new FileValidator();

        $this->repository = new UploadedFileRepository(
            $this->database
        );

        $this->repository->initialize();
        $this->repository->initializeExecutionHistory();

        $this->executionHistoryRepository = new ExecutionHistoryRepository(
            $this->database
        );

$this->jobRepository = new JobRepository(
    $this->database
);

$this->connectionRepository = new ConnectionRepository(
    $this->database
);

$this->connectionRepository->initialize();

$hostedAgent = new HostedAgent(
    $this,
    new Scheduler()
);

$this->backupService = new BackupService(
    $hostedAgent,
    $this->jobRepository,
    $this->connectionRepository
);

$cron = new CronEvaluator();

$runner = new JobRunner(
    $this->backupService
);

$this->schedulerEngine = new SchedulerEngine(
    $this->jobRepository,
    $cron,
    $runner
);

$this->schedulerLoop = new SchedulerLoop(
    $this->schedulerEngine
);

$this->schedulerService = new SchedulerService(
    $this->jobRepository,
    $this->backupService
);


        $this->agent = new BackupCenterAgent(
            $this->config,
            $this->logger,
            $this->scanner,
            $this->validator,
            $this->uploadManager,
            $this->repository,
            $this->executionHistoryRepository
        );
    }

    public function database(): Database
    {
        return $this->database;
    }

    public function agent(): BackupCenterAgent
    {
        return $this->agent;
    }

    public function uploadManager(): UploadManager
    {
        return $this->uploadManager;
    }

    public function logger(): Logger
    {
        return $this->logger;
    }

    public function config(): ConfigurationManager
    {
        return $this->config;
    }

    public function executionHistoryRepository(): ExecutionHistoryRepository
    {
        return $this->executionHistoryRepository;
    }

public function backupService(): BackupService
{
    return $this->backupService;
}    

public function schedulerService(): SchedulerService
{
    return $this->schedulerService;
}

public function schedulerEngine(): SchedulerEngine
{
    return $this->schedulerEngine;
}

public function schedulerLoop(): SchedulerLoop
{
    return $this->schedulerLoop;
}

}