<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBlock\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Service\SymfonyFormBuilder;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Security\Http\Csrf\Annotation\IgnoreCsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class BlockPanel extends AbstractController
{
    private ContentTypeRegistry $contentTypeRegistry;
    private SymfonyFormBuilder $formBuilder;

    public function __construct(
        ContentTypeRegistry $contentTypeRegistry,
        SymfonyFormBuilder $formBuilder
    ) {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->formBuilder = $formBuilder;
    }

    /**
     * @IgnoreCsrfToken()
     */
    public function index(string $type, Request $request): Response
    {
        $blockType = $this->contentTypeRegistry->get($type);

        // @todo Info when block type not exists.

        $data = (array) $request->request->get("content_builder_form_{$type}", []);

        $form = $this->formBuilder->createForm($blockType, $data, false);
        $form->handleRequest($request);

        $validatedAndReadyToSave = false;
        $formDescriptor = new ContentTypeFormDescriptor($blockType, $form);

        if ($formDescriptor->isFormValid()) {
            $validatedAndReadyToSave = true;
        }

        $newData = [];

        // @todo Temporary transformation to multiple values.
        foreach ($data as $key => $value) {
            $newData[$key][] = $value;
        }

        return $this->render('@backend/content_block/block-panel/index.tpl', [
            'type' => $type,
            'formDescriptor' => $formDescriptor,
            'data' => $newData,
            'validatedAndReadyToSave' => $validatedAndReadyToSave,
        ]);
    }
}