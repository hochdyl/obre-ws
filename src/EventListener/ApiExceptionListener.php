<?php

namespace App\EventListener;

use App\Service\ValidationService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ApiExceptionListener
{
    /*
     * JSend standard response
     *
     * @see https://github.com/omniti-labs/jsend
     */
    public const HTTP_CREATED = 201;

    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $status = 'error';
        $type = 'message';
        $value = $exception->getMessage();

        $previousException = $exception->getPrevious();

        if ($previousException instanceof ValidationFailedException) {
            $violations = $previousException->getViolations();

            $status = 'fail';
            $type = 'data';
            $value = ValidationService::getViolations($violations);
        }

        $response = new JsonResponse([
            'status' => $status,
            $type => $value
        ]);

        $event->setResponse($response);
    }
}
