<?php

namespace DMT\XmlParser\Source;

use InvalidArgumentException;

/**
 * class StringParser
 *
 * Parses a string into single characters.
 */
final class StringParser implements Parser
{
    private Parser $parser;

    public function __construct(string $source, int $length = 1024)
    {
        if (($stream = @fopen('data://text/plain,' . $source, 'r')) === false) {
            throw new InvalidArgumentException('Could not read string');
        }

        $this->parser = new StreamParser($stream, $length);
    }

    /**
     * @inheritDoc
     */
    public function parse(): iterable
    {
        yield from $this->parser->parse();
    }

    /**
     * @inheritDoc
     */
    public function getStream()
    {
        return $this->parser->getStream();
    }
}
