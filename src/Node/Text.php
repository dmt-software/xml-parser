<?php

namespace DMT\XmlParser\Node;

class Text implements Node, ElementNode
{
    public string $contents;
    public bool $encode;
    public int $depth = 0;

    public function __construct(string $contents, bool $forceCdata = true)
    {
        $this->contents = trim($contents);
        $this->encode = !$forceCdata;
    }

    public function depth(): int
    {
        return $this->depth;
    }

    public function __toString(): string
    {
        if (!$this->encode) {
            return "<![CDATA[$this->contents]]>";
        }
        return htmlentities($this->contents, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);
    }
}