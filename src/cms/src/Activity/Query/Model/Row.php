<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Query\Model;

use DateTime;

/**
 * @author Adam Banaszkiewicz
 */
class Row
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $websiteId;

    /**
     * @var string
     */
    private $message = '';

    /**
     * @var array
     */
    private $context = [];

    /**
     * @var string
     */
    private $translationDomain = 'messages';

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
