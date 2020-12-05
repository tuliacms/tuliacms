<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldsTemplate\Service;

use Tulia\Cms\ContactForms\Application\FieldsParser\FieldsParserInterface;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Domain\DbalFieldPersister;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Domain\DbalFieldsTemplate;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsTemplateViewUpdater
{
    /**
     * @var DbalFieldPersister
     */
    private $fieldPersister;

    /**
     * @var CurrentWebsiteInterface
     */
    private $currentWebsite;

    /**
     * @var FieldsParserInterface
     */
    private $fieldsParser;

    /**
     * @var DbalFieldsTemplate
     */
    private $fieldsTemplate;

    public function __construct(
        FieldsParserInterface $fieldsParser,
        DbalFieldPersister $fieldPersister,
        CurrentWebsiteInterface $currentWebsite,
        DbalFieldsTemplate $fieldsTemplate
    ) {
        $this->fieldPersister = $fieldPersister;
        $this->currentWebsite = $currentWebsite;
        $this->fieldsParser   = $fieldsParser;
        $this->fieldsTemplate = $fieldsTemplate;
    }

    public function update(string $formId, ?string $newTemplate, string $newTemplateLocale): void
    {
        $defaultLocale = $this->currentWebsite->getDefaultLocale()->getCode();
        $templates = $this->fieldsTemplate->getTemplatesForAllLocales($formId);
        $templates = $this->replaceWithNewTemplate($templates, $newTemplate, $newTemplateLocale);

        foreach ($templates as $template) {
            $stream = $this->fieldsParser->parse($template['fields_template']);
            $fields = $stream->allFields();

            foreach ($fields as $key => $field) {
                $fields[$key]['form_id'] = $formId;
                $fields[$key]['locale']  = $template['locale'];
                $fields[$key]['options'] = json_encode($fields[$key]['options']);
            }

            $this->fieldPersister->save(
                $formId,
                $fields,
                $stream->getResult(),
                $template['locale'],
                $defaultLocale
            );
        }
    }

    private function replaceWithNewTemplate(array $templates, ?string $newTemplate, string $newTemplateLocale): array
    {
        $defaultLocale = $this->currentWebsite->getDefaultLocale()->getCode();

        foreach ($templates as $key => $template) {
            $locale = $templates[$key]['locale'] ?? $defaultLocale;

            if ($newTemplateLocale === $locale) {
                $templates[$key]['fields_template'] = $newTemplate;
            }

            $templates[$key]['locale'] = $locale;
        }

        return $templates;
    }
}
