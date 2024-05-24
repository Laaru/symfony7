<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $isApi = $event->getRequest()->attributes->get('is_api', false);
        if (!$isApi)
            return;

        $exception = $event->getThrowable();
        $response = new JsonResponse();

        $response->setData([
            'success' => false,
            'errors' => [
                'type' => get_class($exception),
                'message' => $exception->getMessage()
            ]
        ]);
        $response->setStatusCode(
            method_exists($exception, 'getStatusCode')
                ? $exception->getStatusCode()
                : 500
        );

        $event->setResponse($response);
    }
}
