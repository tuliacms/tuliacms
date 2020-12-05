<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Tulia\Cms\Node\Query\Event\QueryFilterEvent;
use Tulia\Cms\Node\Query\Model\Node;
use Tulia\Component\Templating\EngineInterface;

/**
 * Listener is responsible for rendering node content at frontend pages.
 *
 * @author Adam Banaszkiewicz
 */
class ContentRenderer
{
    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var array
     */
    protected $scopes;

    /**
     * @var array
     */
    private static $cache = [];

    /**
     * @param EngineInterface $engine
     * @param string $environment
     * @param array $scopes
     */
    public function __construct(EngineInterface $engine, string $environment, array $scopes = [])
    {
        $this->engine = $engine;
        $this->environment = $environment;
        $this->scopes = $scopes;
    }

    /**
     * @param QueryFilterEvent $event
     */
    public function handle(QueryFilterEvent $event): void
    {
        if ($event->hasScope($this->scopes) === false) {
            return;
        }

        $nodes = $event->getCollection();

        foreach ($nodes as $node) {
            $this->render($node);
        }
    }

    /**
     * @param Node $node
     */
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

    /**
     * @param Node $node
     *
     * @return object
     */
    private function createStringableObject(Node $node): object
    {
        $object = new class {
            public $engine;
            public $node;
            public $source;
            public $environment;
            public $rendered;

            /**
             * @return string
             */
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

                        if (!$e instanceof \Throwable) {
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

        $object->node   = $node;
        $object->source = $node->getContent();
        $object->engine = $this->engine;
        $object->environment = $this->environment;

        return $object;
    }
}
