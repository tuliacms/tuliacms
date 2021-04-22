<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Tulia\Cms\Node\UserInterface\Web\Form\ScopeEnum;
use Tulia\Component\FormBuilder\AbstractExtension;
use Tulia\Component\FormBuilder\Section\FormRowSection;
use Tulia\Cms\Taxonomy\Application\Model\Term;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultFieldsExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('visibility', FormType\YesNoType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(): array
    {
        $sections = [];

        $sections[] = $section = new FormRowSection('visibility', 'visibility', 'visibility');
        $section->setPriority(1000);
        $section->setGroup('sidebar');

        return $sections;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(object $object, string $scope): bool
    {
        return $object instanceof Term && $scope === ScopeEnum::BACKEND_EDIT;
    }
}
