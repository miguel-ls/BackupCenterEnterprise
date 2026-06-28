<?php

namespace BackupCenter\Core;

use BackupCenter\Models\BackupFile;

class FileScanner
{
    public function scan(string $path): array
    {
        if (!is_dir($path)) {
            throw new \Exception("Directory not found: {$path}");
        }

        $files = [];

        foreach (scandir($path) as $file) {

            if ($file === '.' || $file === '..') {
                continue;
            }

            $fullPath = $path . DIRECTORY_SEPARATOR . $file;

            if (!is_file($fullPath)) {
                continue;
            }

            $files[] = new BackupFile(
                $file,
                $fullPath,
                filesize($fullPath),
                filemtime($fullPath),
                strtolower(pathinfo($file, PATHINFO_EXTENSION))
            );
        }

        usort($files, function (BackupFile $a, BackupFile $b) {
            return $a->getModified() <=> $b->getModified();
        });

        return $files;
    }
}