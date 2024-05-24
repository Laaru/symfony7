<?php

namespace App\Message;

class ProductOperationMessage
{
    private int $productId;
    private string $operation;

    public function __construct(int $productId, string $operation)
    {
        $this->productId = $productId;
        $this->operation = $operation;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }
}
