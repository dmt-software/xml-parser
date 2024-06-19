<?php

namespace DMT\XmlParser\Source;

use InvalidArgumentException;

class GzipStreamParser implements Parser
{
    /** @var resource */
    private $stream;
    private int $length = 1024;

    /**
     * @param resource $stream
     */
    public function __construct($stream, int $length = 1024)
    {
        if (!is_resource($stream)) {
            throw new InvalidArgumentException('Stream is not a resource');
        }
        $this->stream = $stream;
        $this->length = $length;
    }

    /**
     * @inheritDoc
     */
    public function parse(): iterable
    {
        $tmp = inflate_init(ZLIB_ENCODING_GZIP);

        while ($chunk = @fread($this->stream, $this->length)) {
            yield from str_split(inflate_add($tmp, $chunk));
        }

        if ($chunk === false && !feof($this->stream)) {
            throw new \RuntimeException('Could not read from source');
        }
    }
}