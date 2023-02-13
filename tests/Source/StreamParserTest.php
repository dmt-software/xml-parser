<?php

namespace DMT\Test\XmlParser\Source;

use DMT\XmlParser\Source\StreamParser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class StreamParserTest extends TestCase
{
    public function testParse()
    {
        $handle = fopen(__DIR__ . '/../fixtures/books.xml', 'r');
        $parser = new StreamParser($handle);

        $contents = '';
        foreach ($parser->parse() as $char) {
            $contents .= $char;
            $this->assertSame(1, strlen($char));
        }

        fclose($handle);

        $this->assertSame(file_get_contents(__DIR__ . '/../fixtures/books.xml'), $contents);
    }

    public function testNoFileStreamGiven()
    {
        $this->expectException(InvalidArgumentException::class);

        new StreamParser(__FILE__);
    }
}
