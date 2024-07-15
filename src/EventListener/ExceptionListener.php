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
        $value = $exception->getMessage();

        // The file where the exception was thrown
        $filename = pathinfo($exception->getFile())['filename'];

        // Check if exception come from RequestPayloadValueResolver
        if ($filename === pathinfo(RequestPayloadValueResolver::class)['filename']) {
            $code = 400;
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

        // Check if exception come from EntityValueResolver
        if ($filename === pathinfo(EntityValueResolver::class)['filename']) {
            preg_match('/[^\\\\"]+(?=" object)/', $exception->getMessage(), $matches);
            $code = 404;
            $value = "$matches[0] not found";
        }

        if ($filename === pathinfo(NotFoundHttpException::class)['filename']) {
            preg_match('/[^\\\\"]+(?=" object)/', $exception->getMessage(), $matches);
            $code = 404;
            $value = "$matches[0] not found";
        }

        $response = new JsonResponse([
            'status' => $status,
            $type => $value
        ], $code);

        $event->setResponse($response);
    }
}
