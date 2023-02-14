<?php

namespace DMT\Test\XmlParser\Source;

use DMT\XmlParser\Source\StringParser;
use PHPUnit\Framework\TestCase;

class StringParserTest extends TestCase
{
    public function testParse(): void
    {
        $expected = '<?xml version="1.0" encoding="UTF-8">';
        $parser = new StringParser($expected);

        $contents = '';
        foreach ($parser->parse() as $char) {
            $contents .= $char;
            $this->assertSame(1, strlen($char));
        }

        $this->assertSame($expected, $contents);
    }
}
