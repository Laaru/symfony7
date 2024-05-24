<?php

namespace App\Log\Monolog;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class DatabaseHandler extends AbstractProcessingHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    /**
     * @param array|LogRecord $record
     */
    protected function write(array|\Monolog\LogRecord $record): void
    {
        $log = new Log();
        $log->setMessage($record['message']);
        $log->setContext($record['context']);
        $log->setLevel($record['level']);
        $log->setLevelName($record['level_name']);
        $log->setChannel($record['channel']);
        $log->setExtra($record['extra']);
        $log->setFormatted($record['formatted']);
        $log->setDatetime($record['datetime']);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
