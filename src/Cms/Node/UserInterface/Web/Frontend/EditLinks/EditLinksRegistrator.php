<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Frontend\EditLinks;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\EditLinks\Domain\Collection;
use Tulia\Cms\EditLinks\Ports\Domain\EditLinksCollectorInterface;
use Tulia\Cms\Node\Domain\ReadModel\Model\Node;

/**
 * @author Adam Banaszkiewicz
 */
class EditLinksRegistrator implements EditLinksCollectorInterface
{
    protected TranslatorInterface $translator;

    protected RouterInterface $router;

    protected ContentTypeRegistryInterface $registry;

    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        ContentTypeRegistryInterface $registry
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->registry = $registry;
    }

    public function collect(Collection $collection, object $node, array $options = []): void
    {
        try {
            $type = $this->registry->get($node->getType());

            $collection->add('node.edit', [
                'link'  => $this->router->generate('backend.node.edit', [ 'node_type' => $node->getType(), 'id' => $node->getId() ]),
                'label' => $this->translator->trans('editNode', [
                    'node' => mb_strtolower($this->translator->trans($type->getName(), [], 'node')),
                ]),
            ]);
        } catch (\Exception $e) {
            // Do nothing when Node Type not exists.
        }
    }

    public function supports(object $object): bool
    {
        return $object instanceof Node;
    }
}
