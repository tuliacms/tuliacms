<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Cms\Widget\Menu;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderScopeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class MenuForm extends AbstractType
{
    private MenuFinderInterface $menuFinder;

    private TranslatorInterface $translator;

    public function __construct(MenuFinderInterface $menuFinder, TranslatorInterface $translator)
    {
        $this->menuFinder = $menuFinder;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $source = $this->menuFinder->find([], MenuFinderScopeEnum::INTERNAL);
        $menus = [];

        foreach ($source as $item) {
            $menus[$item->getName()] = $item->getId();
        }

        $layout = [
            $this->translator->trans('horizontal', [], 'menu') => 0,
            $this->translator->trans('vertical', [], 'menu') => 1,
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
