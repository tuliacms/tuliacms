<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Settings;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\NodeType;
use Tulia\Cms\Settings\AbstractGroup;

/**
 * @author Adam Banaszkiewicz
 */
class SettingsGroup extends AbstractGroup
{
    /**
     * @var NodeType
     */
    protected $nodeType;

    /**
     * @param NodeType $nodeType
     */
    public function __construct(NodeType $nodeType)
    {
        $this->nodeType = $nodeType;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'node.' . $this->nodeType->getType();
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
        return $this->nodeType->getTranslationDomain();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(): FormInterface
    {
        $data = [
            'per_page' => $this->getOption('node.' . $this->nodeType->getType() . '.per_page', 15),
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
        $this->setOption('node.' . $this->nodeType->getType() . '.per_page', (int) $data['per_page']);

        return true;
    }
}
