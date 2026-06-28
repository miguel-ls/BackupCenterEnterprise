<?php

namespace BackupCenter\Core;

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

            $files[] = [
                'name' => $file,
                'path' => $fullPath,
                'size' => filesize($fullPath),
                'modified' => filemtime($fullPath),
                'extension' => strtolower(pathinfo($file, PATHINFO_EXTENSION))
            ];
        }

        usort($files, function ($a, $b) {
            return $a['modified'] <=> $b['modified'];
        });

        return $files;
    }
}