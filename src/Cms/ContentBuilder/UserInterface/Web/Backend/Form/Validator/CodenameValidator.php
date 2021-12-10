<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\Validator;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CodenameValidator
{
    protected const PATTERN = '/^[0-9a-z_]+$/';

    public function validateNodeType(?string $code, ExecutionContextInterface $context): void
    {
        if ($code && ! preg_match(self::PATTERN, $code)) {
            $context->buildViolation('nodeTypeCodeMustContainOnlyAlphanumsAndUnderline')
                ->setTranslationDomain('content_builder')
                ->addViolation();
        }
    }

    public function validateSectionId(?string $code, ExecutionContextInterface $context): void
    {
        if ($code && ! preg_match(self::PATTERN, $code)) {
            $context->buildViolation('sectionIdCodeMustContainOnlyAlphanumsAndUnderline')
                ->setTranslationDomain('content_builder')
                ->addViolation();
        }
    }

    public function validateFieldId(?string $code, ExecutionContextInterface $context): void
    {
        if ($code && ! preg_match(self::PATTERN, $code)) {
            $context->buildViolation('fieldIdMustContainOnlyAlphanumsAndUnderline')
                ->setTranslationDomain('content_builder')
                ->addViolation();
        }
    }
}
