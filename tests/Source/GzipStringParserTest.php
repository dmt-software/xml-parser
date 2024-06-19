<?php

namespace DMT\Test\XmlParser\Source;

use DMT\XmlParser\Source\GzipStringParser;
use PHPUnit\Framework\TestCase;

class GzipStringParserTest extends TestCase
{
    public function testParse(): void
    {
        $books = file_get_contents(__DIR__ . '/../fixtures/books.xml');
        $handle = fopen('php://memory', 'w+');

        fwrite($handle, zlib_encode($books, ZLIB_ENCODING_GZIP));
        rewind($handle);

        $parser = new GzipStringParser(stream_get_contents($handle));
        $contents = '';
        foreach ($parser->parse() as $char) {
            $contents .= $char;
            $this->assertSame(1, strlen($char));
        }

        fclose($handle);

        $this->assertSame($books, $contents);
    }
}
