<?php

namespace DMT\XmlParser\Node;

class XmlNamespace implements Node, AttributeNode
{
    public string $uri;
    public string $prefix;
    public bool $declared = false;

    public function __construct(string $uri, string $prefix)
    {
        $this->uri = $uri;
        $this->prefix = $prefix;
    }

    public function prefixNodeName(Node $node): void
    {
        if (!property_exists($node, 'name')
            || !strpos($node->name, ':')
            || ($node->namespace ?? null) !== $this->uri
        ) {
            return;
        }

        $node->name = ltrim(str_replace($this->uri, $this->prefix, $node->name), ':');
        $node->prefix = $this->prefix;
    }

    public function __toString(): string
    {
        $name = rtrim('xmlns:' . $this->prefix, ':');

        return sprintf('%s="%s"', $name, $this->uri);
    }
}
