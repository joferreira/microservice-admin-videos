<?php

namespace App\Services\Storage;

use Core\UseCase\Interfaces\FileStorageInterface;

class FileStorage implements FileStorageInterface
{
    /**
     * @param string $path
     * @param array $_FILES[file]
     * @return string
     */
    public function store(string $path, array $file): string
    {
        
    }

    public function delete(string $path): bool
    {
        return true;
    }
}