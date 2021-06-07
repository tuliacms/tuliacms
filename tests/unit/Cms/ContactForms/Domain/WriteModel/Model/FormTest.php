<?php

declare(strict_types=1);

namespace Tulia\Tests\Unit\Cms\ContactForms\Domain\WriteModel\Model;

use PHPUnit\Framework\MockObject\MockObject;
use Tulia\Cms\ContactForms\Application\FieldType\Core\TextType;
use Tulia\Cms\ContactForms\Domain\FieldsParser\FieldsParserInterface;
use Tulia\Cms\ContactForms\Domain\FieldsParser\FieldsStream;
use Tulia\Cms\ContactForms\Domain\WriteModel\Model\Form;
use Tulia\Tests\Unit\TestCase;

/**
 * @author Adam Banaszkiewicz
 */
class FormTest extends TestCase
{
    private const ID = 'a683c0a4-f522-4191-b4cd-5d381d50222b';
    private const WEBSITE = '4c71a9ef-d08d-4ee2-a453-af0751ba19a2';
    private const LOCALE = 'en_US';
    private const DEFAULT_LOCALE = 'en_US';
    private const FOREIGN_LOCALE = 'pl_PL';

    public function testModelHaveEmptyFieldsAndAddingNewFieldsNotRaiseError(): void
    {
        // Arrange
        $parser = $this->mockParser([
            ['name' => 'name', 'type' => TextType::class, 'options' => []],
            ['name' => 'desc', 'type' => TextType::class, 'options' => []],
        ]);
        $model = Form::createNew(self::ID, self::WEBSITE, self::LOCALE, self::DEFAULT_LOCALE);

        // Act
        $model->setFieldsTemplate('', $parser);

        // Assert
        // Success = not throw any exception
        parent::assertTrue(true);
    }

    public function testPersistDifferentFieldsForForeignLocaleThanDefaultLocaleRaiseError(): void
    {
        // Arrange
        $foreignFields = [
            ['name' => 'new', 'type' => TextType::class, 'options' => []],
            ['name' => 'name', 'type' => TextType::class, 'options' => []],
        ];
        $knownFields = [
            ['name' => 'name', 'type' => TextType::class, 'options' => [], 'locale' => self::DEFAULT_LOCALE],
            ['name' => 'desc', 'type' => TextType::class, 'options' => [], 'locale' => self::DEFAULT_LOCALE],
            ['name' => 'name', 'type' => TextType::class, 'options' => [], 'locale' => self::FOREIGN_LOCALE],
            ['name' => 'desc', 'type' => TextType::class, 'options' => [], 'locale' => self::FOREIGN_LOCALE],
        ];
        $parser = $this->mockParser($foreignFields);

        $model = Form::buildFromArray([
            'id' => self::ID,
            'website_id' => self::WEBSITE,
            'locale' => self::FOREIGN_LOCALE,
            'default_locale' => self::DEFAULT_LOCALE,
            'fields' => $knownFields,
        ]);

        // Act
        $model->setFieldsTemplate('', $parser);

        // Assert
        // Success = not throw any exception
        parent::assertTrue(true);
    }

    /**
     * @return MockObject|FieldsParserInterface
     */
    private function mockParser(array $fields)
    {
        $stream = new FieldsStream('');
        $stream->addField($fields[0]['name'], $fields[0]);
        $stream->addField($fields[1]['name'], $fields[1]);
        $parser = $this->createMock(FieldsParserInterface::class);
        $parser->method('parse')->willReturn($stream);

        return $parser;
    }
}
