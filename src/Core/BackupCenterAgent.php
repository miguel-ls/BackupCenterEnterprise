<?php

namespace BackupCenter\Core;

use BackupCenter\Services\UploadManager;
use BackupCenter\Core\Database;
use BackupCenter\Repositories\UploadedFileRepository;

class BackupCenterAgent
{
    private UploadedFileRepository $repository;
    private UploadManager $uploadManager;
    private ConfigurationManager $config;
    private Logger $logger;
    private FileScanner $scanner;
    private FileValidator $validator;
    private WinScpProvider $provider;

    public function __construct(
        ConfigurationManager $config,
        Logger $logger,
        FileScanner $scanner,
        FileValidator $validator,
        WinScpProvider $provider,
        UploadManager $uploadManager
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->scanner = $scanner;
        $this->validator = $validator;
        $this->provider = $provider;
        $this->uploadManager = $uploadManager;

        $database = new Database(__DIR__ . '/../../storage/database/backupcenter.db');

        $this->repository = new UploadedFileRepository($database);

        $this->repository->initialize();        
    }

    public function run(): void
    {
        $this->logger->info('=== Backup Center iniciado ===');

        $files = $this->scanner->scan(
            $this->config->get('backup.local_path'),
            $this->config->get('backup.extensions', [])
        );

        echo "Ruta: " . $this->config->get('backup.local_path') . PHP_EOL;
        echo "Archivos encontrados: " . count($files) . PHP_EOL;

        foreach ($files as $file) {

            if (!$this->validator->validate($file)) {
                continue;
            }

            $this->logger->info(
                'Subiendo: ' . $file->getName()
            );

            $sha256 = $this->repository->calculateSha256($file->getPath());

            if ($this->repository->exists($sha256)) {
                echo "Omitido: " . $file->getName() . " (ya fue subido)" . PHP_EOL;
                continue;
            }

            echo "Subiendo: " . $file->getName() . PHP_EOL;

            $result = $this->uploadManager->upload($file);

            echo $result . PHP_EOL;

            $this->repository->save(
                $file->getName(),
                $file->getSize(),
                $sha256
            );            

            }

        $this->logger->success('Proceso finalizado.');
        echo PHP_EOL . "Proceso finalizado correctamente." . PHP_EOL;
    }
}