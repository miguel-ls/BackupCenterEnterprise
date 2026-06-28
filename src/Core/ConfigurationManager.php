<?php

namespace BackupCenter\Core;

class ConfigurationManager
{
    private array $config = [];

    public function __construct(string $configFile)
    {
        if (!file_exists($configFile)) {
            throw new \Exception("Configuration file not found: {$configFile}");
        }

        $json = file_get_contents($configFile);

        $this->config = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON configuration.");
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);

        $value = $this->config;

        foreach ($keys as $item) {

            if (!isset($value[$item])) {
                return $default;
            }

            $value = $value[$item];
        }

        return $value;
    }

    public function all(): array
    {
        return $this->config;
    }
}