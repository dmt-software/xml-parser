<?php

namespace DMT\XmlParser;

use DMT\XmlParser\Node\Attribute;
use DMT\XmlParser\Node\Element;
use DMT\XmlParser\Node\Text;
use DMT\XmlParser\Node\XmlNamespace;
use DMT\XmlParser\Node\XmlNamespaces;
use DMT\XmlParser\Source\Parser;
use Generator;

class Tokenizer
{
    public const XML_DROP_NAMESPACES = 1;
    public const XML_USE_CDATA = 2;

    private Parser $parser;
    private int $flags;
    private XmlNamespaces $namespaces;

    /** @var resource */
    private $handle;
    private int $depth = 0;
    private ?Text $textNode = null;
    private ?Element $current = null;

    public function __construct(Parser $parser, string $encoding = null, int $flags = 0)
    {
        $this->parser = $parser;
        $this->flags = $flags;
        $this->namespaces = new XmlNamespaces();

        $this->handle = xml_parser_create_ns($encoding ?? 'UTF-8');
        xml_parser_set_option($this->handle, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($this->handle, XML_OPTION_SKIP_WHITE, 1);
        xml_set_object($this->handle, $this);
        xml_set_start_namespace_decl_handler($this->handle, 'namespace');
        xml_set_element_handler($this->handle, 'open', 'close');
        xml_set_character_data_handler($this->handle, 'contents');
    }

    public function tokenize(): Generator
    {
        foreach ($this->parser->parse() as $data) {
            $this->current = null;
            $this->textNode = null;

            if (!xml_parse($this->handle, $data, '' === $data)) {
                break;
            }

            if ($this->current) {
                $this->current->depth = $this->depth;
                yield $this->current;
            }

            if ($this->textNode) {
                $this->textNode->depth = $this->depth + 1;
                yield $this->textNode;
            }
        }
    }

    private function namespace($parser, string $prefix, string $uri): int
    {
        if (!($this->flags & self::XML_DROP_NAMESPACES)) {
            $this->namespaces[] = new XmlNamespace($uri, $prefix);
        }

        return 1;
    }

    private function open($parser, string $name, array $attributes): void
    {
        foreach ($attributes as $attribute => &$value) {
            $value = new Attribute($attribute, $value);
            if ($value->namespace && $namespace = $this->namespaces->find($value->namespace)) {
                $namespace->prefixNodeName($value);
            }
        }

        $node = new Element($name, $attributes);
        if ($node->namespace && $namespace = $this->namespaces->find($node->namespace)) {
            $namespace->prefixNodeName($node);
        }

        foreach ($this->namespaces as $namespace) {
            if (!$namespace->declared) {
                $node->addAttribute($namespace);
            }
        }

        $this->current = $node;
        $this->depth++;
    }

    private function contents($parser, string $text)
    {
        if (trim($text)) {
            $this->textNode = new Text($text, ($this->flags & self::XML_USE_CDATA));
        }
    }

    private function close($parser, string $name): void
    {
        $this->depth--;

        if (!$this->current) {
            return;
        }

        $namespaces = $this->current->namespaces();
        foreach ($namespaces as $namespace) {
            if (!in_array($this->namespaces->pop(), $namespaces)) {
                throw new \RuntimeException('Namespace declaration lost');
            }
        }
    }
}
