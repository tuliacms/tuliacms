<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Policy;

use Tulia\Cms\ContactForms\Application\FieldsParser\FieldsParserInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultFieldsTemplatePolicy implements FieldsTemplatePolicyInterface
{
    /**
     * @var FieldsParserInterface
     */
    private $fieldsParser;

    /**
     * @param FieldsParserInterface $fieldsParser
     */
    public function __construct(FieldsParserInterface $fieldsParser)
    {
        $this->fieldsParser = $fieldsParser;
    }

    /**
     * {@inheritdoc}
     */
    public function templateCanBeApplied(?string $template): bool
    {
        /**
         * When template contains any errors, the exceptions will be thrown,
         * and will be catched by controller. We only execut parsing.
         *
         * Also, parser data are stored in static cache, so we can parse the same
         * template multiple times, and get the first, the right parse result,
         * without any performance issues.
         */
        $this->fieldsParser->parse((string) $template);

        return true;
    }
}
