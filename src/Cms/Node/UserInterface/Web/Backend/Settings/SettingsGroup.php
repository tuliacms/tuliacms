<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Settings;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Settings\Domain\Group\AbstractSettingsGroup;

/**
 * @author Adam Banaszkiewicz
 */
class SettingsGroup extends AbstractSettingsGroup
{
    protected ContentType $contentType;

    public function __construct(ContentType $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'node.' . $this->contentType->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'nodes';
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon(): string
    {
        return 'fas fa-file-powerpoint';
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomain(): string
    {
        return 'node';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(): FormInterface
    {
        $data = [
            'per_page' => $this->getOption('node.' . $this->contentType->getCode() . '.per_page', 15),
        ];

        return $this->createForm(SettingsForm::class, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(): array
    {
        return $this->view('@backend/node/settings.tpl');
    }

    /**
     * {@inheritdoc}
     */
    public function saveAction(array $data): bool
    {
        $this->setOption('node.' . $this->contentType->getCode() . '.per_page', (int) $data['per_page']);

        return true;
    }
}
