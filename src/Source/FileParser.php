<?php

namespace DMT\XmlParser\Source;

use InvalidArgumentException;

/**
 * class FileStreamParser
 *
 * Parses the data from a file path or file wrapper into characters.
 */
final class FileParser implements Parser
{
    private Parser $parser;

    public function __construct(string $file, int $length = 1024)
    {
        if (($stream = @fopen($file, 'r')) === false) {
            throw new InvalidArgumentException('Could not open file');
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
}
