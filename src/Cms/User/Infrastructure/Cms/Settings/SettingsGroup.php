<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Cms\Settings;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Settings\Domain\Group\AbstractSettingsGroup;

/**
 * @author Adam Banaszkiewicz
 */
class SettingsGroup extends AbstractSettingsGroup
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon(): string
    {
        return 'fas fa-users';
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomain(): string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(): FormInterface
    {
        $data = [
            'password_min_length'        => $this->getOption('users.password.min_length', 4),
            'password_min_digits'        => $this->getOption('users.password.min_digits', 1),
            'password_min_special_chars' => $this->getOption('users.password.min_special_chars', 1),
            'password_min_big_letters'   => $this->getOption('users.password.min_big_letters', 1),
            'password_min_small_letters' => $this->getOption('users.password.min_small_letters', 1),
            'username_min_length'        => $this->getOption('users.username.min_length', 4),
        ];

        /*$this->options->create('users.password.min_length', '4', false, false);
        $this->options->create('users.password.min_digits', '1', false, false);
        $this->options->create('users.password.min_special_chars', '1', false, false);
        $this->options->create('users.password.min_big_letters', '1', false, false);
        $this->options->create('users.password.min_small_letters', '1', false, false);
        $this->options->create('users.username.min_length', '4', false, false);*/

        return $this->createForm(SettingsForm::class, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(): array
    {
        return $this->view('@backend/user/settings.tpl');
    }

    /**
     * {@inheritdoc}
     */
    public function saveAction(array $data): bool
    {
        $this->setOption('users.password.min_length', $data['password_min_length']);
        $this->setOption('users.password.min_digits', $data['password_min_digits']);
        $this->setOption('users.password.min_special_chars', $data['password_min_special_chars']);
        $this->setOption('users.password.min_big_letters', $data['password_min_big_letters']);
        $this->setOption('users.password.min_small_letters', $data['password_min_small_letters']);
        $this->setOption('users.username.min_length', $data['username_min_length']);

        return true;
    }
}
