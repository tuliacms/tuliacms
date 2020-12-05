<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Cms\Widget\Predefined\Menu;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Menu\Application\Query\Finder\Enum\ScopeEnum;
use Tulia\Cms\Menu\Application\Query\Finder\FinderFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuForm extends AbstractType
{
    protected $finderFactory;

    /**
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(FinderFactoryInterface $finderFactory)
    {
        $this->finderFactory = $finderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::INTERNAL);
        $finder->fetchRaw();

        $menus = [];

        foreach ($finder->getResult() as $item) {
            $menus[$item->getName()] = $item->getId();
        }

        $layout = [
            'Horizontal' => 0,
            'Vertical' => 1,
        ];

        $builder
            ->add('menu_id', Type\ChoiceType::class, [
                'constraints' => [
                    new Assert\Uuid(),
                    new Assert\NotBlank(),
                    new Assert\Choice([ 'choices' => $menus ]),
                ],
                'choices' => $menus,
                'choice_translation_domain' => false,
            ])
            ->add('layout', Type\ChoiceType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Choice([ 'choices' => $layout ]),
                ],
                'choices' => $layout,
                'choice_translation_domain' => false,
            ]);
    }
}
