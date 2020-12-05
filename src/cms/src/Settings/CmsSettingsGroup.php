<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Settings\UI\Web\Form\SettingsForm;

/**
 * @author Adam Banaszkiewicz
 */
class CmsSettingsGroup extends AbstractGroup
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'cms';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(): FormInterface
    {
        $data = [
            'website_name'        => $this->getOption('website_name'),
            'website_favicon'     => $this->getOption('website_favicon'),
            'administrator_email' => $this->getOption('administrator_email'),
            'maintenance_mode'    => $this->getOption('maintenance_mode'),
            'maintenance_message' => $this->getOption('maintenance_message'),
            'date_format'         => $this->getOption('date_format', 'j F, Y'),
            'theme'               => $this->getOption('theme'),
            'wysiwyg_editor'      => $this->getOption('wysiwyg_editor'),
            'mail_transport'      => $this->getOption('mail.transport'),
            'mail_from_email'     => $this->getOption('mail.from_email'),
            'mail_from_name'      => $this->getOption('mail.from_name'),
            'mail_host'           => $this->getOption('mail.host'),
            'mail_port'           => $this->getOption('mail.port'),
            'mail_username'       => $this->getOption('mail.username'),
            'mail_password'       => $this->getOption('mail.password'),
            'mail_encryption'     => $this->getOption('mail.encryption'),
            'mail_sendmailpath'   => $this->getOption('mail.sendmailpath'),
            'url_suffix'          => $this->getOption('url_suffix'),
        ];

        /*$this->options->create('website_name', '', true, true);
        $this->options->create('website_favicon', '', false, false);
        $this->options->create('administrator_email', '', false, false);
        $this->options->create('maintenance_mode', '', false, true);
        $this->options->create('maintenance_message', '', true, false);
        $this->options->create('date_format', '', false, true);
        $this->options->create('theme', '', false, true);
        $this->options->create('wysiwyg_editor', '', false, true);
        $this->options->create('mail.transport', '', false, false);
        $this->options->create('mail.from_email', '', false, false);
        $this->options->create('mail.from_name', '', false, false);
        $this->options->create('mail.host', '', false, false);
        $this->options->create('mail.port', '', false, false);
        $this->options->create('mail.username', '', false, false);
        $this->options->create('mail.password', '', false, false);
        $this->options->create('mail.encryption', '', false, false);
        $this->options->create('mail.sendmailpath', '', false, false);
        $this->options->create('url_suffix', '/', false, true);*/

        return $this->createForm(SettingsForm::class, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(): array
    {
        return $this->view('@backend/settings/main-settings.tpl');
    }

    /**
     * {@inheritdoc}
     */
    public function saveAction(array $data): bool
    {
        $this->setOption('website_name', $data['website_name']);
        $this->setOption('website_favicon', $data['website_favicon']);
        $this->setOption('administrator_email', $data['administrator_email']);
        $this->setOption('maintenance_mode', $data['maintenance_mode']);
        $this->setOption('maintenance_message', $data['maintenance_message']);
        $this->setOption('date_format', $data['date_format']);
        $this->setOption('theme', $data['theme']);
        $this->setOption('wysiwyg_editor', $data['wysiwyg_editor']);
        $this->setOption('mail.transport', $data['mail_transport']);
        $this->setOption('mail.from_email', $data['mail_from_email']);
        $this->setOption('mail.from_name', $data['mail_from_name']);
        $this->setOption('mail.host', $data['mail_host']);
        $this->setOption('mail.port', $data['mail_port']);
        $this->setOption('mail.username', $data['mail_username']);

        if ($data['mail_password']) {
            $this->setOption('mail.password', $data['mail_password']);
        }

        $this->setOption('mail.encryption', $data['mail_encryption']);
        $this->setOption('mail.sendmailpath', $data['mail_sendmailpath']);
        $this->setOption('url_suffix', $data['url_suffix']);

        return true;
    }
}
