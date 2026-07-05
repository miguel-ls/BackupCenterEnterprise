<?php

namespace BackupCenter\Scheduler;

use Cron\CronExpression;

class CronEvaluator
{
    public function isDue(string $expression): bool
    {
        return (new CronExpression($expression))->isDue();
    }
}