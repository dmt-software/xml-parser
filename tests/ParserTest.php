<?php

namespace DMT\Test\XmlParser;

use DMT\XmlParser\Node\ElementNode;
use DMT\XmlParser\Node\Text;
use DMT\XmlParser\Parser;
use DMT\XmlParser\Source\FileParser;
use DMT\XmlParser\Source\StringParser;
use DMT\XmlParser\Tokenizer;
use DMT\XmlParser\Tokenizer\XmlParserTokenizer;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testParse(): void
    {
        $parser = new Parser(new XmlParserTokenizer(new StringParser('<book><title>A title</title><author/></book>')));

        $elements = [];
        while ($element = $parser->parse()) {
            $this->assertInstanceOf(ElementNode::class, $element);
            $elements[] = $element->localName ?? $element->contents;
        }

        $this->assertSame(['book', 'title', 'A title', 'author'], $elements);
    }

    public function testParseXml()
    {
        $parser = new Parser(new XmlParserTokenizer(new FileParser(__DIR__ . '/fixtures/books.xml')));

        $books = [];
        while ($element = $parser->parse()) {
            if ($element->localName === 'book') {
                $books[] = $parser->parseXml();
            }
        }

        $this->assertCount(2, $books);
    }

    public function testParseXmlWithInheritNamespaces()
    {
        $xml = '<books xmlns:ns1="urn:ns-uri" xmlns:x="lang">
            <ns1:book xmlns:x="alt-lang">
                <ns1:title x:lang="en_US">A title</ns1:title>
                <author/>
            </ns1:book>
        </books>';

        $parser = new Parser(new XmlParserTokenizer(new StringParser($xml)));

        while ($element = $parser->parse()) {
            if ($element->localName === 'book') {
                $book = $parser->parseXml();
            }
        }

        $this->assertStringContainsString('xmlns:ns1="urn:ns-uri"', $book);
        $this->assertStringContainsString('xmlns:x="alt-lang"', $book);
    }

    public function testDropNamespaces()
    {
        $xml = '<book xmlns="http://example.org/ns" xmlns:ns1="http://example.org/ns">
            <ns1:title>A title</ns1:title>
            <ns1:author/>
        </book>';

        $xml = (new Parser(new XmlParserTokenizer(new StringParser($xml), null, Tokenizer::XML_DROP_NAMESPACES)))->parseXml();

        $this->assertStringNotContainsString('ns1', $xml);
        $this->assertStringNotContainsString('xmlns', $xml);
    }

    public function testUseCData()
    {
        $xml = '<book><title>A title</title><author/></book>';

        $parser = new Parser(new XmlParserTokenizer(new StringParser($xml), null, Tokenizer::XML_USE_CDATA));
        do {
            $element = $parser->parse();
        } while (!$element instanceof Text);

        $this->assertMatchesRegularExpression('~\<\!\[CDATA\[.*\]\]\>~', strval($element));
    }
}
