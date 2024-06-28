<?php

namespace DMT\Test\XmlParser\Tokenizer;

use DMT\XmlParser\Node\ElementNode;
use DMT\XmlParser\Source\StringParser;
use DMT\XmlParser\Tokenizer\XmlReaderTokenizer;
use PHPUnit\Framework\TestCase;

class XmlReaderTokenizerTest extends TestCase
{
    public function testTokenize(): void
    {
        $tokenizer = new XmlReaderTokenizer(new StringParser('<book><title>A title</title><author/></book>'));

        $elements = [];
        foreach($tokenizer->tokenize() as $element) {
            $this->assertInstanceOf(ElementNode::class, $element);
            $elements[] = $element->localName ?? $element->contents;
        }

        $this->assertSame(['book', 'title', 'A title', 'author'], $elements);
    }
}
