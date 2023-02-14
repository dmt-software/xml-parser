<?php

namespace DMT\XmlParser;

use DMT\XmlParser\Node\Element;
use DMT\XmlParser\Node\ElementNode;
use DMT\XmlParser\Node\XmlNamespace;
use DMT\XmlParser\Node\XmlNamespaces;
use Generator;

class Parser
{
    private ?ElementNode $current = null;
    private Generator $iterator;
    private XmlNamespaces $namespaces;

    /**
     * @param \DMT\XmlParser\Tokenizer $tokenizer
     */
    public function __construct(Tokenizer $tokenizer)
    {
        $this->iterator = $tokenizer->tokenize();
        $this->namespaces = $tokenizer->namespaces();
    }

    public function parse(): ?ElementNode
    {
        try {
            return $this->current = $this->iterator->current();
        } finally {
            $this->iterator->next();
        }
    }

    public function parseXml(): string
    {
        $depth = $this->current ? $this->current->depth() : 1;
        $elements = [$depth => $this->current ?? $this->parse()];

        /** @var ElementNode $node */
        while ($node = $this->iterator->current()) {
            if (array_key_exists($node->depth() - 1, $elements)) {
                $elements[$node->depth() - 1]->appendChild($node);
            }
            if ($node->depth() <= $this->current->depth() && $node !== $this->current) {
                break;
            }
            $elements[$node->depth()] = $node;
            $this->iterator->next();
        }

        if ($this->namespaces->count() > 0 && $this->current instanceof Element) {
            $this->applyParentNamespaces($this->current);
        }

        return strval($this->current);
    }

    private function applyParentNamespaces(Element $element): void
    {
        $prefixes = [];
        foreach ($element->namespaces() As $namespace) {
            $prefixes[] = $namespace->prefix;
        }

        $namespaces = array_filter(
            $this->namespaces->getArrayCopy(),
            fn (XmlNamespace $namespace) => !in_array($namespace->prefix, $prefixes)
        );

        foreach ($namespaces as $namespace) {
            $element->addAttribute($namespace);
        }
    }
}
