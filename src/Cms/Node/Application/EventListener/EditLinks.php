<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Model\Node;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\EditLinks\Application\Event\CollectEditLinksEvent;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class EditLinks implements EventSubscriberInterface
{
    protected TranslatorInterface $translator;
    protected RouterInterface $router;
    protected RegistryInterface $registry;

    public function __construct(TranslatorInterface $translator, RouterInterface $router, RegistryInterface $registry)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->registry = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CollectEditLinksEvent::class => ['handle', 0],
        ];
    }

    public function handle(CollectEditLinksEvent $event): void
    {
        /** @var Node $node */
        $node = $event->getObject();

        if (!$node instanceof Node) {
            return;
        }

        try {
            $type = $this->registry->getType($node->getType());

            $event->add('node.edit', [
                'link'  => $this->router->generate('backend.node.edit', [ 'node_type' => $node->getType(), 'id' => $node->getId() ]),
                'label' => $this->translator->trans('editNode', [
                    'node' => mb_strtolower($this->translator->trans('node', [], $type->getTranslationDomain())),
                ]),
            ]);
        } catch (\Exception $e) {
            // Do nothing when Node Type not exists.
        }
    }
}
