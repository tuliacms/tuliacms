<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Form\UserForm;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints\EmailUnique;
use Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints\Username;
use Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints\UsernameUnique;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Component\FormBuilder\Manager\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class UserForm extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', Type\HiddenType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Uuid(),
                ],
            ])
            ->add('username', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Username(),
                    new UsernameUnique(['id_not_in_fields' => ['id']]),
                ],
            ])
            ->add('email', Type\EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                    new EmailUnique(['id_not_in_fields' => ['id']]),
                ],
            ])
            ->add('cancel', FormType\CancelType::class, [
                'route' => 'backend.user',
            ])
            ->add('save', FormType\SubmitType::class)
        ;

        if ($options['form_extension_manager'] instanceof ManagerInterface) {
            $options['form_extension_manager']->buildForm($builder, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'form_extension_manager' => null,
            'password_required'      => true,
        ]);
    }
}
