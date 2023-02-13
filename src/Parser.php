<?php

namespace DMT\XmlParser;

use DMT\XmlParser\Node\ElementNode;
use DMT\XmlParser\Node\Node;
use Generator;

class Parser
{
    private ?ElementNode $current = null;
    private ?Generator $iterator = null;

    /**
     * @param \DMT\XmlParser\Tokenizer $tokenizer
     */
    public function __construct(Tokenizer $tokenizer)
    {
        if (!$this->iterator) {
            $this->iterator = $tokenizer->tokenize();
        }
    }

    public function parse(): ?Node
    {
        try {
            return $this->current = $this->iterator->current();
        } finally {
            $this->iterator->next();
        }
    }

    public function parseXml(): string
    {
        $elements[$this->current->depth()] = $this->current ?? $this->parse();
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

        return strval($this->current);
    }
}
