<?php

namespace BackupCenter\WindowsService;

use BackupCenter\Core\Application;

class ServiceRunner
{
    private Application $app;

    public function __construct()
    {
        $this->app = new Application();
    }

    public function run(): void
    {
        echo "==========================================" . PHP_EOL;
        echo "Backup Center Enterprise Service" . PHP_EOL;
        echo "==========================================" . PHP_EOL;

        while (true) {

            try {

                $this->app
                    ->scheduler()
                    ->execute();

                $this->app
                    ->worker()
                    ->process();

            } catch (\Throwable $e) {

                echo "[" .
                    date("Y-m-d H:i:s") .
                    "] " .
                    $e->getMessage() .
                    PHP_EOL;

            }

            sleep(5);

        }

    }

}