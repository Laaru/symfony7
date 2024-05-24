<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;


    protected function registerExceptionHandlers()
    {
        parent::registerExceptionHandlers();

        $this->setExceptionHandler(function (\Throwable $e) {
            if ($e instanceof NotFoundHttpException) {
                return new JsonResponse([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 404);
            }

            // Handle other exceptions if needed

            return parent::handleException($e);
        });
    }
}
