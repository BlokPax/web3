<?php

namespace Blokpax\Web3;

class Transaction
{
    public function __construct(
        protected readonly string $to,
        protected readonly AbiEntry $entry,
        protected readonly array $params = []
    ) {}

    public function toArray(): array
    {
        return [
            'to'   => $this->to,
            'data' => $this->entry->encode($this->params),
        ];
    }

    public function decode(string $data): mixed
    {
        return $this->entry->decode($data);
    }
}
