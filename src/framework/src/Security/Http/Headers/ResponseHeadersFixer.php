<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Http\Headers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Framework\Kernel\Event\ResponseEvent;

/**
 * @author Adam Banaszkiewicz
 */
class ResponseHeadersFixer implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => ['removeHeaders', 0],
        ];
    }

    public function removeHeaders(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        header_remove('X-Powered-By');
        header_remove('Server');

        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'SameOrigin');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
    }
}
