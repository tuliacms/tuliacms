<?php

declare(strict_types=1);

namespace Tulia\Cms\TuliaEditor\UserInterface\Web\Backend\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tulia\Cms\Node\UserInterface\Web\Backend\Form\NodeForm;
use Tulia\Component\FormSkeleton\Extension\AbstractExtension;

/**
 * @author Adam Banaszkiewicz
 */
class NodeFormExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('tulia_editor_data', Type\HiddenType::class, [
            'property_path' => 'tulia-editor-data',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool
    {
        return $formType instanceof NodeForm;
    }
}
