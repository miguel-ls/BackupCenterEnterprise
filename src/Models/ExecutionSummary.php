<?php

namespace BackupCenter\Models;

class ExecutionSummary
{
    public int $found = 0;
    public int $uploaded = 0;
    public int $skipped = 0;
    public int $errors = 0;

    private float $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function getDuration(): float
    {
        return round(microtime(true) - $this->startTime, 2);
    }
}