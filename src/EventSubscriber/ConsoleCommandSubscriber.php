<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class ConsoleCommandSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => 'onCommand',
            ConsoleEvents::TERMINATE => 'onTerminate',
            ConsoleEvents::ERROR => 'onError',
        ];
    }

    public function onCommand(ConsoleCommandEvent $event)
    {
        $command = $event->getCommand();
        $input = $event->getInput();

        $this->logger->info(sprintf('Command "%s" started with input: %s', $command->getName(), json_encode($input->getArguments())));
    }

    public function onTerminate(ConsoleTerminateEvent $event)
    {
        $command = $event->getCommand();
        $exitCode = $event->getExitCode();

        $this->logger->info(sprintf('Command "%s" finished with exit code: %s', $command->getName(), $exitCode));
    }

    public function onError(ConsoleErrorEvent $event)
    {
        $command = $event->getCommand();
        $error = $event->getError();

        $this->logger->error(sprintf('Command "%s" failed with error: %s', $command->getName(), $error->getMessage()));
    }
}
