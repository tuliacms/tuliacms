<?php

declare(strict_types=1);

namespace spec\Tulia\Cms\ContactForms\Infrastructure\Persistence\Domain;

use PhpSpec\ObjectBehavior;
use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalFormPersisterSpec extends ObjectBehavior
{
    private const DEFAULT_LOCALE = 'en_US';

    private static $form = [
        'en_US' => [
            'input' => [
                'id' => 'id',
                'receivers' => ['receivers'],
                'senderName' => 'senderName',
                'senderEmail' => 'senderEmail',
                'replyTo' => 'replyTo',
                'name' => 'Form en_US',
                'subject' => 'subject',
                'messageTemplate' => 'messageTemplate',
                'fieldsSource' => 'fieldsSource',
                'fieldsTemplate' => 'fieldsTemplate',
                'websiteId' => 'websiteId',
                'locale' => 'en_US',
            ],
            'output' => [
                'main' => [
                    'id' => 'id',
                    'website_id' => 'websiteId',
                    'receivers' => '["receivers"]',
                    'sender_name' => 'senderName',
                    'sender_email' => 'senderEmail',
                    'reply_to' => 'replyTo',
                ],
                'lang' => [
                    'form_id' => 'id',
                    'locale' => 'en_US',
                    'name' => 'Form en_US',
                    'subject' => 'subject',
                    'message_template' => 'messageTemplate',
                    'fields_source' => 'fieldsSource',
                    'fields_template' => 'fieldsTemplate',
                ],
            ],
        ],
        'pl_PL' => [
            'input' => [
                'id' => 'id',
                'receivers' => ['receivers'],
                'senderName' => 'senderName',
                'senderEmail' => 'senderEmail',
                'replyTo' => 'replyTo',
                'name' => 'Form pl_PL',
                'subject' => 'subject',
                'messageTemplate' => 'messageTemplate',
                'fieldsSource' => 'fieldsSource',
                'fieldsTemplate' => 'fieldsTemplate',
                'websiteId' => 'websiteId',
                'locale' => 'pl_PL',
            ],
            'output' => [
                'main' => [
                    'id' => 'id',
                    'website_id' => 'websiteId',
                    'receivers' => '["receivers"]',
                    'sender_name' => 'senderName',
                    'sender_email' => 'senderEmail',
                    'reply_to' => 'replyTo',
                ],
                'lang' => [
                    'form_id' => 'id',
                    'locale' => 'pl_PL',
                    'name' => 'Form pl_PL',
                    'subject' => 'subject',
                    'message_template' => 'messageTemplate',
                    'fields_source' => 'fieldsSource',
                    'fields_template' => 'fieldsTemplate',
                ],
            ],
        ],
    ];

    public function let(ConnectionInterface $connection): void
    {
        $this->beConstructedWith($connection);
    }

    public function it_should_insert_main_row_when_not_exists_with_default_locale(
        ConnectionInterface $connection
    ): void {
        $input  = static::$form[self::DEFAULT_LOCALE]['input'];
        $output = $this->buildMainMergedOutputForInsert(static::$form[self::DEFAULT_LOCALE]['output']);

        $this->mainRowShouldNotExists($connection);
        $this->langRowShouldNotExists($connection, self::DEFAULT_LOCALE);

        $connection->insert('#__form', $output['main'])
            ->shouldBeCalledOnce();

        $this->save($input, self::DEFAULT_LOCALE);
    }

    public function it_should_insert_main_and_locale_rows_when_not_exists_with_foreign_locale(
        ConnectionInterface $connection
    ): void {
        $input  = static::$form['pl_PL']['input'];
        $output = $this->buildMainMergedOutputForInsert(static::$form['pl_PL']['output']);

        $this->mainRowShouldNotExists($connection);
        $this->langRowShouldNotExists($connection, 'pl_PL');

        $connection->insert('#__form', $output['main'])
            ->shouldBeCalledOnce();
        $connection->insert('#__form_lang', $output['lang'])
            ->shouldBeCalledOnce();

        $this->save($input, self::DEFAULT_LOCALE);
    }

    public function it_should_update_main_row_with_default_locale(
        ConnectionInterface $connection
    ): void {
        $input  = static::$form[self::DEFAULT_LOCALE]['input'];
        $output = $this->buildMainMergedOutputForUpdate(static::$form[self::DEFAULT_LOCALE]['output']);

        $this->mainRowShouldExists($connection);
        $this->langRowShouldExists($connection, self::DEFAULT_LOCALE);

        $connection->update('#__form', $output['main'], ['id' => 'id'])
            ->shouldBeCalledOnce();

        $this->save($input, self::DEFAULT_LOCALE);
    }

    public function it_should_update_main_row_and_insert_lang_row_when_not_exists_with_foreign_locale(
        ConnectionInterface $connection
    ): void {
        $input  = static::$form['pl_PL']['input'];
        $output = $this->buildMainOutputForUpdate(static::$form['pl_PL']['output']);

        $this->mainRowShouldExists($connection);
        $this->langRowShouldNotExists($connection, 'pl_PL');

        $connection->update('#__form', $output['main'], ['id' => 'id'])
            ->shouldBeCalledOnce();
        $connection->insert('#__form_lang', $output['lang'])
            ->shouldBeCalledOnce();

        $this->save($input, self::DEFAULT_LOCALE);
    }

    public function it_should_update_main_row_and_update_lang_row_with_foreign_locale(
        ConnectionInterface $connection
    ): void {
        $input  = static::$form['pl_PL']['input'];
        $output = $this->buildMainOutputForUpdate(static::$form['pl_PL']['output']);
        $output = $this->buildLangOutputForUpdate($output);

        $this->mainRowShouldExists($connection);
        $this->langRowShouldExists($connection, 'pl_PL');

        $connection->update('#__form', $output['main'], ['id' => 'id'])
            ->shouldBeCalledOnce();
        $connection->update('#__form_lang', $output['lang'], ['form_id' => 'id', 'locale' => 'pl_PL'])
            ->shouldBeCalledOnce();

        $this->save($input, self::DEFAULT_LOCALE);
    }

    private function buildMainMergedOutputForInsert(array $output): array
    {
        $output['main'] = array_merge($output['main'], $output['lang']);

        unset(
            $output['main']['locale'],
            $output['main']['form_id']
        );

        return $output;
    }

    private function buildMainMergedOutputForUpdate(array $output): array
    {
        $output = $this->buildMainMergedOutputForInsert($output);

        unset(
            $output['main']['id'],
            $output['main']['website_id']
        );

        return $output;
    }

    private function buildMainOutputForUpdate(array $output): array
    {
        unset(
            $output['main']['id'],
            $output['main']['website_id']
        );

        return $output;
    }

    private function buildLangOutputForUpdate(array $output): array
    {
        unset(
            $output['lang']['form_id'],
            $output['lang']['locale']
        );

        return $output;
    }

    private function mainRowShouldExists(ConnectionInterface $connection): void
    {
        $connection->fetchAllAssociative('SELECT id FROM #__form WHERE id = :id LIMIT 1', ['id' => 'id'])
            ->shouldBeCalledOnce()
            ->willReturn([['id' => 'id']]);
    }

    private function mainRowShouldNotExists(ConnectionInterface $connection): void
    {
        $connection->fetchAllAssociative('SELECT id FROM #__form WHERE id = :id LIMIT 1', ['id' => 'id'])
            ->shouldBeCalledOnce()
            ->willReturn([]);
    }

    private function langRowShouldExists(ConnectionInterface $connection, string $locale): void
    {
        $connection->fetchAllAssociative(
            'SELECT form_id FROM #__form_lang WHERE form_id = :id AND locale = :locale LIMIT 1',
            ['id' => 'id', 'locale' => $locale]
        )
            ->shouldBeCalledOnce()
            ->willReturn([['form_id' => 'id']]);
    }

    private function langRowShouldNotExists(ConnectionInterface $connection, string $locale): void
    {
        $connection->fetchAllAssociative(
            'SELECT form_id FROM #__form_lang WHERE form_id = :id AND locale = :locale LIMIT 1',
            ['id' => 'id', 'locale' => $locale]
        )
            ->shouldBeCalledOnce()
            ->willReturn([]);
    }
}
