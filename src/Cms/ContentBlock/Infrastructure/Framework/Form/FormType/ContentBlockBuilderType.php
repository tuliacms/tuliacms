<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBlock\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Json;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeRegistry;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBlockBuilderType extends AbstractType
{
    private ContentTypeRegistry $contentTypeRegistry;

    public function __construct(ContentTypeRegistry $contentTypeRegistry)
    {
        $this->contentTypeRegistry = $contentTypeRegistry;
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
                ];
            }
        }

        $view->vars['label'] = null;
        $view->vars['block_types'] = $types;
        dump($view->vars);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'content_block_builder';
    }
}
