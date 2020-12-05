<?php

declare(strict_types=1);

/**
 * @param string $tagname
 *
 * @return string
 */
function tagged(string $tagname): string
{
    return '!tagged:' . $tagname;
}

/**
 * @param string $service
 *
 * @return string
 */
function service(string $service): string
{
    return '@' . $service;
}

/**
 * @param string $name
 *
 * @return string
 */
function parameter(string $name, bool $optional = false): string
{
    return '%' . ($optional ? '?' : '') . $name;
}

/**
 * @param string $tag
 * @param int $priority
 * @param array $parameters
 *
 * @return array
 */
function tag(string $tag, int $priority = 10, array $parameters = []): array
{
    $parameters['name'] = $tag;
    $parameters['priority'] = $priority;

    return $parameters;
}

/**
 * @param string $event
 * @param int $priority
 * @param string|null $method
 *
 * @return array
 */
function tag_event_listener(string $event, int $priority = 0, string $method = null): array
{
    return [
        'name'     => 'event_listener',
        'event'    => $event,
        'method'   => $method,
        'priority' => $priority,
    ];
}

/**
 * @param string $name
 *
 * @return array
 */
function tag_console_command(string $name): array
{
    return [
        'name' => 'console.command',
        'command' => $name,
    ];
}

/**
 * @param string $action
 * @param string $method
 * @param int $priority
 *
 * @return array
 */
function tag_action(string $action, string $method, int $priority = 0): array
{
    return [
        'name'     => 'hook',
        'action'   => $action,
        'method'   => $method,
        'priority' => $priority,
    ];
}

/**
 * @param string $filter
 * @param string $method
 * @param int $priority
 *
 * @return array
 */
function tag_filter(string $filter, string $method, int $priority = 0): array
{
    return [
        'name'     => 'hook',
        'filter'   => $filter,
        'method'   => $method,
        'priority' => $priority,
    ];
}

/**
 * @return array
 */
function tag_widget(): array
{
    return [
        'name' => 'widget',
    ];
}

/**
 * @param string $method
 * @param array $arguments
 *
 * @return array
 */
function call(string $method, array $arguments = []): array
{
    return [
        'method'    => $method,
        'arguments' => $arguments,
    ];
}
