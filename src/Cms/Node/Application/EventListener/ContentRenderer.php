<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Model\Node;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Event\QueryFilterEvent;
use Tulia\Component\Templating\EngineInterface;

/**
 * Listener is responsible for rendering node content at frontend pages.
 * @author Adam Banaszkiewicz
 */
class ContentRenderer implements EventSubscriberInterface
{
    private EngineInterface $engine;

    private string $environment;

    private array $scopes;

    private static array $cache = [];

    public function __construct(EngineInterface $engine, string $environment, array $scopes = [])
    {
        $this->engine = $engine;
        $this->environment = $environment;
        $this->scopes = $scopes;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            QueryFilterEvent::class => ['handle', 0],
        ];
    }

    public function handle(QueryFilterEvent $event): void
    {
        if ($event->hasScope($this->scopes) === false) {
            return;
        }

        foreach ($event->getCollection() as $node) {
            $this->render($node);
        }
    }

    private function render(Node $node): void
    {
        $content = $node->getContent();
        $key = md5($content);

        if (isset(static::$cache[$key])) {
            $node->setContent(static::$cache[$key]);
            return;
        }

        if ($content) {
            $content = $this->createStringableObject($node);
            $node->setContent($content);
            static::$cache[$key] = $content;
        }
    }

    private function createStringableObject(Node $node): object
    {
        $object = new class {
            public $engine;
            public $node;
            public $source;
            public $environment;
            public $rendered;

            public function __toString(): string
            {
                if ($this->rendered) {
                    return $this->rendered;
                }

                try {
                    $this->rendered = $this->engine->renderString(
                        $this->source,
                        [ 'this' => $this->node ],
                        'Node ' . $this->node->getId()
                    );
                } catch (\Throwable $exception) {
                    if ($this->environment === 'prod') {
                        $this->rendered = 'Cannot render Node content due to an internal content error.';
                    } else {
                        $e = $exception->getPrevious();

                        if (! $e instanceof \Throwable) {
                            $e = $exception;
                        }

                        $this->rendered .='Cannot render Node content. ';
                        $this->rendered .= '<i><b>' . str_replace('Error occured creating string template: ', '', $e->getMessage()) . '</b></i>';
                        $this->rendered .= sprintf(
                            "<pre>In %s on line %d.\nTrace:\n%s</pre>",
                            $e->getFile(),
                            $e->getLine(),
                            $e->getTraceAsString()
                        );
                    }
                }

                return $this->rendered;
            }
        };

        $object->node = $node;
        $object->source = $node->getContent();
        $object->engine = $this->engine;
        $object->environment = $this->environment;

        return $object;
    }
}
