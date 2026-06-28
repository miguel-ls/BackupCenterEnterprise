<?php

namespace BackupCenter\Core;

class Application
{
    private ConfigurationManager $config;
    private Logger $logger;

    public function __construct()
    {
        $this->config = new ConfigurationManager(
            Paths::config() . '/config.json'
        );

        $this->logger = new Logger(
            Paths::logs()
        );         
    }


    public function logger(): Logger
    {
        return $this->logger;
    }
        
    public function config(): ConfigurationManager
    {
        return $this->config;
    }
}