<?php

declare(strict_types=1);

namespace Tulia\Component\Security\Http\Csrf\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken as SymfonyToken;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tulia\Cms\Shared\Infrastructure\Utils\Annotations\AnnotationsReader;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Security\Http\Csrf\Annotation\IgnoreCsrfToken;
use Tulia\Component\Security\Http\Csrf\Exception\RequestCsrfTokenException;

/**
 * @author Adam Banaszkiewicz
 */
class ControllerRequestTokenValidator implements EventSubscriberInterface
{
    protected CsrfTokenManagerInterface $csrfManager;
    protected LoggerInterface $logger;

    public function __construct(CsrfTokenManagerInterface $csrfManager, LoggerInterface $logger)
    {
        $this->csrfManager = $csrfManager;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'handle',
        ];
    }

    public function handle(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getMethod() !== 'POST') {
            return;
        }

        $reader = AnnotationsReader::createFromRequest($request);

        if (! $reader instanceof AnnotationsReader) {
            $this->logger->notice(
                'Cannot create AnnotationsReader from Request. Probably controller is not a string definition. Skipping CSRF security.'
            );

            return;
        }

        $tokenInfo = $reader->getMethodAnnotation(CsrfToken::class);

        if (!$tokenInfo instanceof CsrfToken) {
            $ignoreMethod = $reader->getMethodAnnotation(IgnoreCsrfToken::class);
            $ignoreClass  = $reader->getClassAnnotation(IgnoreCsrfToken::class);

            if ($ignoreMethod instanceof IgnoreCsrfToken || $ignoreClass instanceof IgnoreCsrfToken) {
                $this->logger->notice(sprintf(
                    'IgnoreCsrfToken annotation discovered. Skipping CSRF security for %s.',
                    $reader->getController() . '::' . $reader->getMethod()
                ));

                return;
            }

            $message = sprintf(
                'Missing %s annotation, in %s controller action. All actions that handle POST request, must support CSRF security.',
                CsrfToken::class,
                $reader->getController() . '::' . $reader->getMethod()
            );

            $this->logger->error($message);
            throw new RequestCsrfTokenException($message);
        }

        $searchFor = [];

        if ($tokenInfo->path === null) {
            // Default token place
            $searchFor[] = '_token';
            // Mimic Symfony form path
            $searchFor[] = $tokenInfo->id . '._token';
        }

        $searchIn = [
            $request->request->all(),
            $request->query->all()
        ];

        $value = $this->searchForValue($searchFor, $searchIn);

        if (empty($value)) {
            $message = sprintf(
                'Missing CSRF token in POST Request. Searched for \'%s\' in request, but nothing found.',
                implode(', ', $searchFor)
            );

            $this->logger->critical($message);
            throw new RequestCsrfTokenException($message);
        }

        $token = new SymfonyToken($tokenInfo->id, $value);

        if ($this->csrfManager->isTokenValid($token)) {
            return;
        }

        $message = sprintf(
            'CSRF token was found in request on path \'%s\', but it does not match that one from CSRF manager. Please check if it\'s ID defined in controller\'s annotation is correct.',
            $tokenInfo->path
        );

        $this->logger->critical($message);
        throw new RequestCsrfTokenException($message);
    }

    /**
     * @param array $searchFor
     * @param array $searchIn
     *
     * @return mixed|null
     */
    private function searchForValue(array $searchFor, array $searchIn)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($searchFor as $path) {
            $pathPrepared = '[' . str_replace('.', '][', $path) . ']';

            foreach ($searchIn as $resource) {
                $value = $accessor->getValue($resource, $pathPrepared);

                if ($value !== null) {
                    return $value;
                }
            }
        }

        return null;
    }
}
