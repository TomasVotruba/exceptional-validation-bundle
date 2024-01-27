<?php

declare(strict_types=1);

namespace PhPhD\ExceptionalValidation\Model;

use function implode;

final class PropertyPath
{
    public function __construct(
        /** @var list<string> */
        private array $items,
    ) {
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function with(string $item): self
    {
        return new self([...$this->items, $item]);
    }

    public function toString(): string
    {
        return implode('.', $this->items);
    }
}
