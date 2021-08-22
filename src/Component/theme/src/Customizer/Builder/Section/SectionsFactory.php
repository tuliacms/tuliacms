<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Section;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SectionsFactory implements SectionsFactoryInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function create(string $id, array $params = []): SectionInterface
    {
        $params['label'] = $this->translator->trans($params['label'], [], $params['translation_domain']);

        return new Section($id, $params);
    }
}
