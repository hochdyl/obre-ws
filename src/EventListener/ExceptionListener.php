<?php

namespace App\EventListener;

use App\Service\ValidationService;
use Symfony\Bridge\Doctrine\ArgumentResolver\EntityValueResolver;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestPayloadValueResolver;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
final class ExceptionListener
{
    /*
     * JSend format response
     *
     * @see https://github.com/omniti-labs/jsend
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $exceptionCode = $exception->getCode();

        $code = $exceptionCode >= 300 && $exceptionCode < 600 ? $exception->getCode() : 500;
        $status = 'error';
        $type = 'message';
        $value = 'An error occurred in our servers';

        // The file where the exception was thrown
        $filename = pathinfo($exception->getFile())['filename'];

        if ($exception instanceof NotFoundHttpException) {
            preg_match('/[^\\\\"]+(?=" object)/', $exception->getMessage(), $matches);
            $code = 404;
            $value = "$matches[0] not found";
        }

        else if ($filename === pathinfo(RequestPayloadValueResolver::class)['filename']) {
            $code = 400;
            $value = $exception->getMessage();

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
        ], $code);

        $event->setResponse($response);
    }
}
