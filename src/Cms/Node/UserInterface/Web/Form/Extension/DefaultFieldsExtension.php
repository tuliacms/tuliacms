<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Node\UserInterface\Web\Form\ScopeEnum;
use Tulia\Component\FormBuilder\AbstractExtension;
use Tulia\Component\FormBuilder\Section\Section;
use Tulia\Cms\Node\Application\Model\Node;
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
        $builder
            ->add('publishedAt', FormType\DateTimeType::class, [
                'label' => 'publishedAt',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('publishedTo', FormType\DateTimeType::class, [
                'label' => 'publicationEndsAt',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(): array
    {
        $sections = [];

        $sections[] = $section = new Section('status', 'statusAndAvailability', '@backend/node/parts/status.tpl');
        $section->setPriority(1000);
        $section->setGroup('sidebar');
        $section->setFields(['status', 'publishedAt', 'publishedTo']);

        return $sections;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(object $object, string $scope): bool
    {
        return $object instanceof Node && $scope === ScopeEnum::BACKEND_EDIT;
    }
}