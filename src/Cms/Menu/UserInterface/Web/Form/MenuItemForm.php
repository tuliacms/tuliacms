<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\Type\RegistryInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\Type\TypeInterface;
use Tulia\Cms\Menu\Infrastructure\Framework\Form\FormType\MenuItemChoiceType;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Component\FormBuilder\Manager\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItemForm extends AbstractType
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param RegistryInterface $registry
     * @param TranslatorInterface $translator
     */
    public function __construct(
        RegistryInterface $registry,
        TranslatorInterface $translator
    ) {
        $this->registry   = $registry;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $itemTargets = [
            $this->trans('itemTargetAuto')  => '',
            $this->trans('itemTargetSelf')  => '_self',
            $this->trans('itemTargetBlank') => '_blank',
        ];

        $itemTypes = [];

        /** @var TypeInterface $type */
        foreach ($this->registry->all() as $type) {
            $itemTypes[$this->trans($type->getLabel(), $type->getTranslationDomain())] = $type->getType();
        }

        $builder
            ->add('id', Type\HiddenType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Uuid(),
                ],
            ])
            ->add('name', Type\TextType::class, [
                'label' => 'name',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('identity', Type\HiddenType::class)
            ->add('visibility', FormType\YesNoType::class, [
                'label' => 'visibility',
            ])
            ->add('menuId', Type\HiddenType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Uuid(),
                ],
            ])
            ->add('type', Type\ChoiceType::class, [
                'label' => 'itemType',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Choice([ 'choices' => $itemTypes ]),
                ],
                'choices' => $itemTypes,
                'translation_domain' => 'menu',
                'choice_translation_domain' => false,
            ])
            ->add('hash', Type\TextType::class, [
                'label' => 'itemHash',
                'translation_domain' => 'menu',
            ])
            ->add('target', Type\ChoiceType::class, [
                'label' => 'itemTarget',
                'constraints' => [
                    new Assert\Choice([ 'choices' => $itemTargets ]),
                ],
                'choices' => $itemTargets,
                'translation_domain' => 'menu',
                'choice_translation_domain' => 'menu',
            ])
            ->add('save', FormType\SubmitType::class, [
                'mapped' => false,
            ]);

        if ($options['persist_mode'] === 'create') {
            $builder->add('parentId', MenuItemChoiceType::class, [
                'label' => 'parentItem',
                'menu_id' => $options['data']->getMenuId(),
                'empty_option' => true,
                'empty_option_label' => 'rootItemSpecial',
                'empty_option_translation_domain' => 'menu',
                'translation_domain' => 'menu',
            ]);
        }

        if ($options['form_extension_manager'] instanceof ManagerInterface) {
            $options['form_extension_manager']->buildForm($builder, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('persist_mode', 'create');
        $resolver->setDefault('form_extension_manager', null);
    }

    /**
     * @param string $id
     * @param string $domain
     *
     * @return string
     */
    private function trans(string $id, string $domain = 'menu'): string
    {
        return $this->translator->trans($id, [], $domain);
    }
}
