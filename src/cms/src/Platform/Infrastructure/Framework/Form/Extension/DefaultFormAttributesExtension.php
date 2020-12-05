<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultFormAttributesExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($form->isRoot() === false) {
            return;
        }

        $view->vars['attr'] = array_merge($view->vars['attr'], [
            /**
             * By default we prevent HTML5 browser form validation.
             */
            'novalidate' => 'novalidate',
            /**
             * Set form ID to form tag. This is used to send form directly in JavaScript
             * using form's ID.
             */
            'id' => $view->vars['id'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
