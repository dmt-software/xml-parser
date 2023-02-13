<?php

namespace DMT\XmlParser\Source;

use InvalidArgumentException;

/**
 * class StringParser
 *
 * Parses an xml string into single characters.
 */
final class StringParser implements Parser
{
    private string $source;
    private int $length = 1024;

    public function __construct(string $source, int $length = 1024)
    {
        $this->source = $source;
        $this->length = $length;
    }

    /**
     * @inheritDoc
     */
    public function parse(): iterable
    {
        $chunks = str_split($this->source, $this->length);

        foreach ($chunks as $chunk) {
            yield from str_split($chunk);
        }
    }
}
