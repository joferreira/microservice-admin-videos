<?php

namespace Core\Domain\ValueObject;

use Core\Domain\Enum\MediaStatus as EnumMediaStatus;
use Exception;

class Media 
{
    public function __construct(
        protected string $filePath,
        protected EnumMediaStatus $mediaStatus,
        protected string $encodedPath = '',
    ) {}

    public function __get($property)
    {
        return $this->{$property};
    }
}