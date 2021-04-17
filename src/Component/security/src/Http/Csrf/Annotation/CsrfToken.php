<?php

declare(strict_types=1);

namespace Tulia\Component\Security\Http\Csrf\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 * @author Adam Banaszkiewicz
 */
final class CsrfToken
{
    /**
     * Token name which will be searched in Request.
     *
     * @Required
     * @var string
     */
    public $id;

    /**
     * Path where in request the token is stored. By default, token is searched in `_token`,
     * but You can change this place, in example:
     * 1. In POST request, we can store token in Symfony form, so we need to define
     *    this form prefix, like: `node_form._token`.
     * 2. In GET request, we can define custom query parameter, like: `_token_name`.
     *
     * @var string
     */
    public $path;
}
