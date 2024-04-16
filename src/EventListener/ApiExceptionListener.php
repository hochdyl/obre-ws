<?php

namespace App\EventListener;

use App\Service\ValidationService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestPayloadValueResolver;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
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

        // The file where the exception was thrown
        $filename = pathinfo($exception->getFile())['filename'];

        // Check if exception come from RequestPayloadValueResolver
        if ($filename === pathinfo(RequestPayloadValueResolver::class)['filename']) {
            $value = 'Request body is missing or improperly formatted';

            // Check if exception come from ValidationFailedException
            $previousException = $exception->getPrevious();
            if ($previousException instanceof ValidationFailedException) {
                $violations = $previousException->getViolations();

                $status = 'fail';
                $type = 'data';
                $value = ValidationService::getViolations($violations);
            }
        }

        $response = new JsonResponse([
            'status' => $status,
            $type => $value
        ]);

        $event->setResponse($response);
    }
}
