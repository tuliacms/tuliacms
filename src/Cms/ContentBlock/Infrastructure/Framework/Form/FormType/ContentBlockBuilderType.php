<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBlock\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Json;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBlockBuilderType extends AbstractType
{
    private ContentTypeRegistry $contentTypeRegistry;
    private RouterInterface $router;

    public function __construct(
        ContentTypeRegistry $contentTypeRegistry,
        RouterInterface $router
    ) {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->router = $router;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('compound', false);
        $resolver->setDefault('constraints', [
            new Json()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $types = [];

        foreach ($this->contentTypeRegistry->all() as $type) {
            if ($type->isType('content_block')) {
                $icon = null;

                foreach ($type->getFields() as $field) {
                    if ($field->isType('___content_block_icon')) {
                        $icon = $field->getConfig('icon');
                    }
                }

                $types[$type->getCode()] = [
                    'code' => $type->getCode(),
                    'name' => $type->getName(),
                    'icon' => $icon,
                    'block_panel_url' => $this->router->generate('backend.content_block.block_panel.builder', [ 'type' => $type->getCode() ]),
                ];
            }
        }

        $view->vars['label'] = null;
        $view->vars['block_types'] = $types;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'content_block_builder';
    }
}
