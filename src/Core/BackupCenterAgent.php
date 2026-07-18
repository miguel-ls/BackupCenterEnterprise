<?php

namespace BackupCenter\Core;

use BackupCenter\Contracts\IConfiguration;
use BackupCenter\Models\ExecutionSummary;
use BackupCenter\Repositories\ExecutionHistoryRepository;
use BackupCenter\Repositories\UploadedFileRepository;
use BackupCenter\Services\UploadManager;

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
        $client = $configuration->get('client.name')
            ?? $configuration->get('name')
            ?? 'Sin nombre';

        Audit::info(
            "AGENT",
            "START",
            "Inicio respaldo {$client}",
            "SYSTEM"
        );

        $this->logger->info('=== Backup Center iniciado ===');

        $summary = new ExecutionSummary();

        $path = $configuration->get('backup.local_path')
            ?? $configuration->get('source');

        $extensions = $configuration->get('backup.extensions')
            ?? $this->config->get('backup.extensions')
            ?? ['zip','bak','7z'];

        $files = $this->scanner->scan(
            $path,
            $extensions
        );

        $summary->found = count($files);

        echo "Ruta: {$path}" . PHP_EOL;
        echo "Archivos encontrados: {$summary->found}" . PHP_EOL;

        foreach($files as $file){

            if(!$this->validator->validate($file)){
                continue;
            }

            if($this->lockValidator->isLocked($file->getPath())){

                $summary->skipped++;

                $this->logger->info(
                    "Archivo en uso: ".$file->getName()
                );

                Audit::info(
                    "AGENT",
                    "SKIPPED",
                    $file->getName()." bloqueado",
                    "SYSTEM"
                );

                continue;
            }

            if(
                $this->repository->exists(
                    (int)$configuration->get('id'),
                    $file->getName(),
                    $file->getSize()
                )
            ){

                $summary->skipped++;

                Audit::info(
                    "AGENT",
                    "SKIPPED",
                    $file->getName()." ya fue enviado",
                    "SYSTEM"
                );

                continue;
            }

            try{

                $this->uploadManager->upload($file, $configuration);

                $sha256 = $this->repository->calculateSha256(
                    $file->getPath()
                );

                $this->repository->save(

                    (int)$configuration->get('id'),

                    $file->getName(),

                    $file->getSize(),

                    $sha256

                );

                $summary->uploaded++;

                Audit::info(

                    "UPLOAD",

                    "SUCCESS",

                    $file->getName(),

                    "SYSTEM"

                );

            }catch(\Throwable $e){

                $summary->errors++;

                $this->logger->error($e->getMessage());

                Audit::error(

                    "UPLOAD",

                    "FAILED",

                    $file->getName()." : ".$e->getMessage(),

                    "SYSTEM"

                );

                continue;
            }

        }

        $summary->finish();

        $this->executionHistoryRepository->save(

            $summary,

            $client

        );

        Audit::info(

            "AGENT",

            "FINISH",

            "Encontrados {$summary->found}, Subidos {$summary->uploaded}, Omitidos {$summary->skipped}, Errores {$summary->errors}",

            "SYSTEM"

        );

        $this->logger->success("Proceso finalizado.");

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