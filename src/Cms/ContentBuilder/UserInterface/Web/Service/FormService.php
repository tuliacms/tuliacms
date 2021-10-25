<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Service;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\FormDescriptor;

/**
 * @author Adam Banaszkiewicz
 */
class FormService
{
    private NodeTypeRegistry $nodeTypeRegistry;
    private SymfonyFormBuilder $formBuilder;

    public function __construct(
        NodeTypeRegistry $nodeTypeRegistry,
        SymfonyFormBuilder $formBuilder
    ) {
        $this->nodeTypeRegistry = $nodeTypeRegistry;
        $this->formBuilder = $formBuilder;
    }

    public function buildFormDescriptor(string $type, array $nodeData, Request $request): FormDescriptor
    {
        $nodeType = $this->nodeTypeRegistry->get($type);

        $form = $this->formBuilder->createForm($nodeType, $nodeData);
        $form->handleRequest($request);

        return new FormDescriptor(
            $nodeType,
            $form
        );
    }
}
