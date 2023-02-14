<?php

namespace DMT\Test\XmlParser\Node;

use DMT\XmlParser\Node\Text;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    /**
     * @dataProvider provideText
     */
    public function testText(Text $text, string $asString):void
    {
        $this->assertSame($asString, strval($text));
    }

    public function provideText(): iterable
    {
        return [
            'simple text node' => [
                new Text('<text-node>', true),
                '<![CDATA[<text-node>]]>'
            ],
            'encode text node' => [
                new Text('<text-node>'),
                '&lt;text-node&gt;'
            ]
        ];
    }
}
