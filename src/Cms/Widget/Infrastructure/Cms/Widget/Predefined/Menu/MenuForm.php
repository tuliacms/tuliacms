<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Cms\Widget\Predefined\Menu;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\Enum\ScopeEnum;
use Tulia\Cms\Menu\Ports\Domain\ReadModel\MenuFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuForm extends AbstractType
{
    protected MenuFinderInterface $menuFinder;

    public function __construct(MenuFinderInterface $menuFinder)
    {
        $this->menuFinder = $menuFinder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $source = $this->menuFinder->find([], ScopeEnum::INTERNAL);
        $menus = [];

        foreach ($source as $item) {
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
