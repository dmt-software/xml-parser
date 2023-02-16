<?php

namespace DMT\XmlParser\Node;

class Text implements Node, ElementNode
{
    public string $contents;
    public bool $encode;
    public int $depth = 0;

    public function __construct(string $contents, bool $forceCDATA = false)
    {
        $this->contents = trim($contents);
        $this->encode = !$forceCDATA;
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

        if (!preg_match('~[\<\>]~', $this->contents)) {
            return $this->contents;
        }

       return htmlentities($this->contents, ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8', false);
    }
}
