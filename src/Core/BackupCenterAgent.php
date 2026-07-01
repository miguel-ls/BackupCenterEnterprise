<?php

namespace BackupCenter\Core;

use BackupCenter\Services\UploadManager;
use BackupCenter\Repositories\UploadedFileRepository;
use BackupCenter\Models\ExecutionSummary;
use BackupCenter\Repositories\ExecutionHistoryRepository;

class BackupCenterAgent
{
    private FileLockValidator $lockValidator;
    private UploadedFileRepository $repository;
    private UploadManager $uploadManager;
    private ConfigurationManager $config;
    private Logger $logger;
    private FileScanner $scanner;
    private FileValidator $validator;
    private ExecutionHistoryRepository $executionHistoryRepository;


    public function __construct(
        ConfigurationManager $config,
        Logger $logger,
        FileScanner $scanner,
        FileValidator $validator,
        UploadManager $uploadManager,
        UploadedFileRepository $repository,
        ExecutionHistoryRepository $executionHistoryRepository
        
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->scanner = $scanner;
        $this->validator = $validator;
        $this->uploadManager = $uploadManager;
        $this->lockValidator = new FileLockValidator();
        $this->executionHistoryRepository = $executionHistoryRepository;
        $this->repository = $repository;    
    }

    public function run(): ExecutionSummary
    {
        $this->logger->info('=== Backup Center iniciado ===');
        $summary = new ExecutionSummary();

        $files = $this->scanner->scan(
            $this->config->get('backup.local_path'),
            $this->config->get('backup.extensions', [])
        );

        $summary->found = count($files);


        echo "Ruta: " . $this->config->get('backup.local_path') . PHP_EOL;
        echo "Archivos encontrados: " . count($files) . PHP_EOL;

        foreach ($files as $file) {

            if (!$this->validator->validate($file)) {
                continue;
            }

            if ($this->lockValidator->isLocked($file->getPath())) {
                echo "Omitido: " . $file->getName() . " (archivo en uso)" . PHP_EOL;
                $summary->skipped++;

                $this->logger->info(
                    "Archivo en uso: " . $file->getName()
                );

                continue;
            }            

            $this->logger->info(
                'Subiendo: ' . $file->getName()
            );

            $sha256 = $this->repository->calculateSha256($file->getPath());

            if ($this->repository->exists($sha256)) {

                $summary->skipped++;

                echo "Omitido: " . $file->getName() . " (ya fue subido)" . PHP_EOL;

                continue;
            }

            echo "Subiendo: " . $file->getName() . PHP_EOL;

            try {

                $result = $this->uploadManager->upload($file);

                echo $result . PHP_EOL;

                $this->repository->save(
                    $file->getName(),
                    $file->getSize(),
                    $sha256
                );

                $summary->uploaded++;

            } catch (\Throwable $e) {

                $summary->errors++;

                echo "ERROR: " . $e->getMessage() . PHP_EOL;

                $this->logger->error(
                    "Error subiendo {$file->getName()}: {$e->getMessage()}"
                );

                continue;
            }

            }

        $summary->finish();

        $this->executionHistoryRepository->save(
            $summary,
            $this->config->get('client.name')
        );            

        $this->logger->success('Proceso finalizado.');

        echo PHP_EOL;
        echo "==========================================" . PHP_EOL;
        echo "Backup Center Enterprise" . PHP_EOL;
        echo "==========================================" . PHP_EOL;
        echo "Archivos encontrados : {$summary->found}" . PHP_EOL;
        echo "Subidos              : {$summary->uploaded}" . PHP_EOL;
        echo "Omitidos             : {$summary->skipped}" . PHP_EOL;
        echo "Errores              : {$summary->errors}" . PHP_EOL;
        echo "Tiempo total         : {$summary->getDuration()} s" . PHP_EOL;
        echo "==========================================" . PHP_EOL;

        return $summary;
    }
}