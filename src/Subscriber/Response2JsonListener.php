<?php
declare(strict_types=1);

namespace App\Subscriber;

use App\Dto\RestResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Response2JsonListener implements EventSubscriberInterface
{
    use ResponseListenerTrait;

    public function responseSerializer(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $this->addRuntimeHeaders($response);
        if ($response instanceof RestResponse) {
            if ($response->getErrorResponse()->count() > 0) {
                $response->setJson($this->serialize($response->getErrorResponse()));
            } elseif (null !== $response->getPayload()) {
                $response->setJson($this->serialize($response->getPayload()));
            }
        }

        return $response;
    }

    /**
     * Serialize entity and initializes a new response object with the json content.
     */
    public function viewSerializer(ViewEvent $event): void
    {
        $result = $event->getControllerResult();

        match (true) {
            $result instanceof RestResponse => $event->setResponse($result),
            $result instanceof ConstraintViolationListInterface => $event->setResponse($this->violationListToResponse($result)),
            null === $result => $event->setResponse(
                RestResponse::new(Response::HTTP_NOT_FOUND)->addNewError('Resource not available')
            ),
            default => $event->setResponse(RestResponse::new()->setJson($this->serialize($result))),
        };
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['viewSerializer', 0],
            ],
            KernelEvents::RESPONSE => [
                ['responseSerializer', 0],
            ],
        ];
    }
}
