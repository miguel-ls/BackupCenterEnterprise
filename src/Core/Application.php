<?php

namespace BackupCenter\Core;

use BackupCenter\Builders\ScriptBuilder;
use BackupCenter\Core\WinScpProvider;
use BackupCenter\Core\RetryPolicy;
use BackupCenter\Services\UploadManager;
use BackupCenter\Core\FileScanner;
use BackupCenter\Core\FileValidator;
use BackupCenter\Repositories\UploadedFileRepository;
use BackupCenter\Core\Database;
use BackupCenter\Repositories\ExecutionHistoryRepository;

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
    private BackupCenterAgent $agent;
    private Database $database;

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

    

}