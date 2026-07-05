<?php

namespace BackupCenter\Core;

use BackupCenter\Services\UploadManager;
use BackupCenter\Repositories\UploadedFileRepository;
use BackupCenter\Models\ExecutionSummary;
use BackupCenter\Repositories\ExecutionHistoryRepository;
use BackupCenter\Contracts\IConfiguration;

class BackupCenterAgent
{
    private FileLockValidator $lockValidator;
    private UploadedFileRepository $repository;
    private UploadManager $uploadManager;


private IConfiguration $config;
    private Logger $logger;
    private FileScanner $scanner;
    private FileValidator $validator;
    private ExecutionHistoryRepository $executionHistoryRepository;


    public function __construct(
        IConfiguration $config,
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

    public function run(
    IConfiguration $configuration
): ExecutionSummary
    {
        $this->logger->info('=== Backup Center iniciado ===');
        $summary = new ExecutionSummary();

$path = $configuration->get('backup.local_path')
    ?? $configuration->get('source');

$extensions = $configuration->get('backup.extensions')
    ?? ['zip', 'bak', '7z'];

$files = $this->scanner->scan(
    $path,
    $extensions
);

        $summary->found = count($files);


        echo "Ruta: " . $configuration->get('backup.local_path') . PHP_EOL;
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

            echo "JOB ID = " . $configuration->get('id') . PHP_EOL;

            if (
                $this->repository->exists(
                    (int)$configuration->get('id'),
                    $file->getName(),
                    $file->getSize()
                )
            ) {

                $summary->skipped++;

                echo "Omitido: " . $file->getName() . " (ya fue subido)" . PHP_EOL;

                continue;
            }

            echo "Subiendo: " . $file->getName() . PHP_EOL;

            try {

                $result = $this->uploadManager->upload($file);

                $sha256 = $this->repository->calculateSha256($file->getPath());

                $this->repository->save(
                    (int)$configuration->get('id'),
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
$configuration->get('client.name')
    ?? $configuration->get('name')
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