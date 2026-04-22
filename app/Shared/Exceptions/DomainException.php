<?php

namespace App\Shared\Exceptions;

use Exception;

class DomainException extends Exception
{
    public function __construct(
        string $message,
        private readonly int $status = 422,
        private readonly array $context = [],
    ) {
        parent::__construct($message);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function context(): array
    {
        return $this->context;
    }
}
