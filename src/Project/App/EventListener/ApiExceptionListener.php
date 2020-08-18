<?php

namespace App\Project\App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Class ApiExceptionListener
 * @package App\Project\App\EventListener
 */
class ApiExceptionListener
{
    /**
     * @param ExceptionEvent $event
     * @todo add response factory for different formats, depends on HTTP headers 'Content-Type' or 'Accept'?
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $error = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];
        $event->setResponse(new JsonResponse(['success' => false, 'error' => $error]));
        $event->stopPropagation();
    }
}