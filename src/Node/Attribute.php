<?php

namespace DMT\XmlParser\Node;

class Attribute implements Node, AttributeNode
{
    public string $name;
    public string $localName;
    public ?string $namespace = null;
    public string $prefix = '';
    public string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = trim($name, ':');
        $this->localName = $this->name;
        $this->value = $value;

        $match = [];
        if (preg_match('~^(?<namespace>.+)\:(?<localName>.+)$~', $name, $match)) {
            $this->namespace = $match['namespace'];
            $this->localName = $match['localName'];
        }
    }

    public function __toString(): string
    {
        $name = ($this->prefix ? $this->prefix . ':' : '') . $this->localName;

        return sprintf('%s="%s"', $name, $this->value);
    }
}
