<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Infrastructure\Cms\Widget\Predefined;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\ContactForms\Query\Enum\ScopeEnum;
use Tulia\Cms\ContactForms\Query\FinderFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContactFormForm extends AbstractType
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
        $finder->fetch();

        $menus = [];

        foreach ($finder->getResult() as $item) {
            $menus[$item->getName()] = $item->getId();
        }

        $builder
            ->add('form_id', Type\ChoiceType::class, [
                'label' => 'form',
                'translation_domain' => 'forms',
                'constraints' => [
                    new Assert\Uuid(),
                    new Assert\NotBlank(),
                    new Assert\Choice([ 'choices' => $menus ]),
                ],
                'choices' => $menus,
                'choice_translation_domain' => false,
            ])
        ;
    }
}
