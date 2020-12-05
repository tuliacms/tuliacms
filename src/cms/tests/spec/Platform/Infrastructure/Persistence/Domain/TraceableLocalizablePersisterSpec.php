<?php

declare(strict_types=1);

namespace spec\Tulia\Cms\Platform\Infrastructure\Persistence\Domain;

use PhpSpec\ObjectBehavior;
use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\TraceInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TraceableLocalizablePersisterSpec extends ObjectBehavior
{
    private const DEFAULT_LOCALE = 'en_US';

    private static $form = [
        'en_US' => [
            'id' => 'id',
            'receivers' => ['receivers'],
            'name' => 'Form en_US',
            'locale' => 'en_US',
        ],
        'pl_PL' => [
            'id' => 'id',
            'receivers' => ['receivers'],
            'name' => 'Form pl_PL',
            'locale' => 'pl_PL',
        ],
    ];

    public function let(TraceInterface $trace): void
    {
        $this->beConstructedWith($trace);
    }

    public function it_should_throw_exception_when_missing_data_id(): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('save', [['locale' => self::DEFAULT_LOCALE], self::DEFAULT_LOCALE]);
    }

    public function it_should_throw_exception_when_missing_data_locale(): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('save', [['id' => 'id'], self::DEFAULT_LOCALE]);
    }

    public function it_should_insert_main_row_when_not_exists_with_default_locale(
        TraceInterface $trace
    ): void {
        $data = static::$form[self::DEFAULT_LOCALE];

        $this->mainRowShouldNotExists($trace);
        $this->langRowShouldNotExists($trace, self::DEFAULT_LOCALE);

        $trace->insertMainRow($data)->shouldBeCalledOnce();

        $this->save($data, self::DEFAULT_LOCALE);
    }

    public function it_should_insert_main_and_locale_rows_when_not_exists_with_foreign_locale(
        TraceInterface $trace
    ): void {
        $data = static::$form['pl_PL'];

        $this->mainRowShouldNotExists($trace);
        $this->langRowShouldNotExists($trace, 'pl_PL');

        $trace->insertMainRow($data)->shouldBeCalledOnce();
        $trace->insertLangRow($data)->shouldBeCalledOnce();

        $this->save($data, self::DEFAULT_LOCALE);
    }

    public function it_should_update_main_row_with_default_locale(
        TraceInterface $trace
    ): void {
        $data = static::$form[self::DEFAULT_LOCALE];
        $foreignLocale = false;

        $this->mainRowShouldExists($trace);
        $this->langRowShouldExists($trace, self::DEFAULT_LOCALE);

        $trace->updateMainRow($data, $foreignLocale)->shouldBeCalledOnce();

        $this->save($data, self::DEFAULT_LOCALE);
    }

    public function it_should_update_main_row_and_insert_lang_row_when_not_exists_with_foreign_locale(
        TraceInterface $trace
    ): void {
        $data = static::$form['pl_PL'];
        $foreignLocale = true;

        $this->mainRowShouldExists($trace);
        $this->langRowShouldNotExists($trace, 'pl_PL');

        $trace->updateMainRow($data, $foreignLocale)->shouldBeCalledOnce();
        $trace->insertLangRow($data)->shouldBeCalledOnce();

        $this->save($data, self::DEFAULT_LOCALE);
    }

    public function it_should_update_main_row_and_update_lang_row_with_foreign_locale(
        TraceInterface $trace
    ): void {
        $data = static::$form['pl_PL'];
        $foreignLocale = true;

        $this->mainRowShouldExists($trace);
        $this->langRowShouldExists($trace, 'pl_PL');

        $trace->updateMainRow($data, $foreignLocale)->shouldBeCalledOnce();
        $trace->updateLangRow($data)->shouldBeCalledOnce();

        $this->save($data, self::DEFAULT_LOCALE);
    }

    private function mainRowShouldExists(TraceInterface $trace): void
    {
        $trace->rootExists('id')->shouldBeCalledOnce()->willReturn(true);
    }

    private function mainRowShouldNotExists(TraceInterface $trace): void
    {
        $trace->rootExists('id')->shouldBeCalledOnce()->willReturn(false);
    }

    private function langRowShouldExists(TraceInterface $trace, string $locale): void
    {
        $trace->langExists('id', $locale)->shouldBeCalledOnce()->willReturn(true);
    }

    private function langRowShouldNotExists(TraceInterface $trace, string $locale): void
    {
        $trace->langExists('id', $locale)->shouldBeCalledOnce()->willReturn(false);
    }
}
