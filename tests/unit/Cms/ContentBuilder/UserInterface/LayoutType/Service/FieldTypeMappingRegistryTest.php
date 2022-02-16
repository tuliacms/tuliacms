<?php

declare(strict_types=1);

namespace Tulia\Tests\Unit\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\ConstraintTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Tests\Unit\TestCase;

/**
 * @author Adam Banaszkiewicz
 */
class FieldTypeMappingRegistryTest extends TestCase
{
    public function test_return_mapping_only_for_given_content_type(): void
    {
        $registry = $this->produceRegistry();
        $registry->addMapping('___content_block_icon', [
            'label' => 'Content Block::Icon',
            'classname' => 'Symfony\Component\Form\Extension\Core\Type\TextType',
            'only_for_types' => [ 'content_block' ],
            'exclude_for_types' => [],
            'constraints' => [],
            'custom_constraints' => [],
        ]);
        $registry->addMapping('page_text', [
            'label' => 'Content Block::Icon',
            'classname' => 'Symfony\Component\Form\Extension\Core\Type\TextType',
            'only_for_types' => [],
            'exclude_for_types' => [],
            'constraints' => [],
            'custom_constraints' => [],
        ]);

        $result = $registry->allForContentType('content_block');

        $this->assertArrayHasKey('___content_block_icon', $result);
        $this->assertArrayHasKey('page_text', $result);

        $result = $registry->allForContentType('page');
        $this->assertArrayNotHasKey('___content_block_icon', $result);
        $this->assertArrayHasKey('page_text', $result);
    }

    private function produceRegistry(): FieldTypeMappingRegistry
    {
        $constraintMappingRegistry = \Mockery::mock(ConstraintTypeMappingRegistry::class);

        return new FieldTypeMappingRegistry($constraintMappingRegistry);
    }
}
