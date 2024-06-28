<?php

namespace DMT\XmlParser\Tokenizer;

use DMT\XmlParser\Node\Attribute;
use DMT\XmlParser\Node\Element;
use DMT\XmlParser\Node\Text;
use DMT\XmlParser\Node\XmlNamespace;
use DMT\XmlParser\Node\XmlNamespaces;
use DMT\XmlParser\Source\Parser;
use DMT\XmlParser\Tokenizer;
use Generator;
use InvalidArgumentException;
use XMLReader;

class XmlReaderTokenizer implements Tokenizer
{
    private Parser $parser;
    private int $flags;

    private XmlNamespaces $namespaces;
    private int $depth = 0;
    private XMLReader $reader;

    public function __construct(Parser $parser, string $encoding = null, int $flags = 0)
    {
        $this->parser = $parser;
        $this->flags = $flags;
        $this->namespaces = new XmlNamespaces();

        assert(
            array_key_exists('uri', $this->parser->getMetadata()),
            new InvalidArgumentException('Source parser can not by used for XMLReaderTokenizer')
        );

        $this->reader = new XMLReader();
        $this->reader->open($this->parser->getMetadata()['uri'], $encoding ?? 'UTF-8');
    }

    public function namespaces(): XmlNamespaces
    {
        return $this->namespaces;
    }

    public function tokenize(): Generator
    {
        while ($this->reader->read()) {
            if ($this->reader->nodeType === XMLReader::ELEMENT) {
                $element = new Element($this->reader->name);
                $element->depth = ++$this->depth;
                $this->renderAttributes($element);

                yield $element;
            } elseif ($this->reader->nodeType === XMLReader::TEXT) {
                $text = new Text($this->reader->value, ($this->flags & self::XML_USE_CDATA));
                $text->depth = $this->depth + 1;

                yield $text;
            } elseif ($this->reader->nodeType === XMLReader::END_ELEMENT) {
                $this->depth--;
            }
        }

        $this->reader->close();
    }

    private function renderAttributes(Element $element): void
    {
        while ($this->reader->moveToNextAttribute()) {
            if ($this->flags & self::XML_DROP_NAMESPACES && strpos($this->reader->name, 'xmlns') === 0) {
                continue;
            }

            if (!($this->flags & self::XML_DROP_NAMESPACES)) {
                if ($this->reader->name === 'xmlns') {
                    $this->namespaces->append(new XmlNamespace($this->reader->value, ''));
                } elseif (strpos($this->reader->name, 'xmlns:') === 0) {
                    $this->namespaces->append(new XmlNamespace($this->reader->value, substr($this->reader->name, 6)));
                }
            }

            $element->addAttribute(new Attribute($this->reader->name, $this->reader->value));
        }
    }
}
