<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\Twig\Assetter;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * @author Adam Banaszkiewicz
 */
class AssetterTokenParser extends AbstractTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $values = $this->parser->getExpressionParser()->parseMultitargetExpression();

        $stream->expect(Token::BLOCK_END_TYPE);

        return new AssetterNode(['values' => $values], [], $token->getLine(), 'assetter');
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'assets';
    }
}
