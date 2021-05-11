<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;
use Tulia\Cms\User\UserInterface\Web\Form\MyAccount\MyAccountForm;
use Tulia\Cms\User\UserInterface\Web\Form\UserForm\UserForm;
use Tulia\Component\FormBuilder\Extension\AbstractExtension;
use Tulia\Component\FormBuilder\Section\Section;

/**
 * @author Adam Banaszkiewicz
 */
class AvatarExtension extends AbstractExtension
{
    protected UploaderInterface $uploader;

    public function __construct(UploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar', Type\FileType::class, [
                /**
                 * Mapping of avatar is disabled. Setting filepath is created in form extension.
                 */
                'mapped' => false,
                'constraints' => [
                    new Assert\Image([
                        'minWidth' => 100,
                        'minHeight' => 100,
                        'maxWidth' => 700,
                        'maxHeight' => 700,
                        'allowLandscape' => false,
                        'allowPortrait' => false,
                        'mimeTypes' => ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'],
                    ]),
                ],
            ])
            ->add('remove_avatar', Type\CheckboxType::class, [
                'mapped' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(): array
    {
        $sections = [];

        $sections[] = $section = new Section('avatar', 'avatar', '@backend/user/user/parts/avatar.tpl');
        $section->setPriority(100);
        $section->setFields(['avatar']);

        return $sections;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(FormInterface $form, object $data): void
    {
        $this->uploader->uploadForUser($data, $form);

        if ($form['remove_avatar']->getData()) {
            $this->uploader->removeUploadedForUser($data);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool
    {
        return $formType instanceof UserForm || $formType instanceof MyAccountForm;
    }
}
