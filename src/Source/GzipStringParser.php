<?php

namespace DMT\XmlParser\Source;

class GzipStringParser implements Parser
{
    private string $source;
    private int $length;

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
        $tmp = inflate_init(ZLIB_ENCODING_GZIP);
        $chunks = str_split($this->source, $this->length);

        foreach ($chunks as $chunk) {
            yield from str_split(inflate_add($tmp, $chunk));
        }
    }
}