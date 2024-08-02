<?php

namespace App\EventListener;

use App\Service\ValidationService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestPayloadValueResolver;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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

        $response = new JsonResponse();

        if ($exception instanceof AccessDeniedHttpException) {
            $response->setData([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
            $response->setStatusCode(Response::HTTP_FORBIDDEN);

            $event->setResponse($response);
            return;
        }

        if ($exception instanceof NotFoundHttpException) {
            preg_match('/[^\\\\"]+(?=" object)/', $exception->getMessage(), $matches);
            $resourceName = $matches[0] ?? "Resource";
            $response->setData([
                'status' => 'error',
                'message' => "$resourceName not found",
            ]);
            $response->setStatusCode(Response::HTTP_NOT_FOUND);

            $event->setResponse($response);
            return;
        }

        // The file where the exception was thrown
        $filename = pathinfo($exception->getFile())['filename'];
        if ($filename === pathinfo(RequestPayloadValueResolver::class)['filename']) {
            $response->setData([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);

            // Check if exception come from ValidationFailedException
            $previousException = $exception->getPrevious();
            if ($previousException instanceof ValidationFailedException) {
                $violations = $previousException->getViolations();

                $response->setData([
                    'status' => 'fail',
                    'data' => ValidationService::getViolations($violations)
                ]);
            }

            $event->setResponse($response);
            return;
        }

        $code = $exceptionCode >= 300 && $exceptionCode < 600 ? $exception->getCode() : 500;
        $response->setData([
            'status' => 'error',
            'message' => 'An error occurred in our servers'
        ]);
        $response->setStatusCode($code);

        $event->setResponse($response);
    }
}
