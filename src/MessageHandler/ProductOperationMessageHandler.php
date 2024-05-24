<?php

namespace App\MessageHandler;

use App\Message\ProductOperationMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProductOperationMessageHandler
{

    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    public function __invoke(ProductOperationMessage $message)
    {
        $productId = $message->getProductId();
        $operation = $message->getOperation();

        $this->logger->info("Operation $operation performed on product with ID: $productId");
    }
}
