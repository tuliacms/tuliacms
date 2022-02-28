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

    public function getId(): string
    {
        return 'node.' . $this->contentType->getCode();
    }

    public function getName(): string
    {
        return $this->contentType->getName();
    }

    public function getIcon(): string
    {
        return $this->contentType->getIcon();
    }

    public function getTranslationDomain(): string
    {
        return 'node';
    }

    public function buildForm(): FormInterface
    {
        $data = [
            'per_page' => $this->getOption('node.' . $this->contentType->getCode() . '.per_page', 15),
        ];

        return $this->createForm(SettingsForm::class, $data);
    }

    public function buildView(): array
    {
        return $this->view('@backend/node/settings.tpl');
    }

    public function saveAction(array $data): bool
    {
        $this->setOption('node.' . $this->contentType->getCode() . '.per_page', (int) $data['per_page']);

        return true;
    }
}
