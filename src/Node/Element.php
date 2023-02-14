<?php

namespace DMT\XmlParser\Node;

class Element implements Node, ElementNode
{
    public string $name;
    public string $localName;
    public ?string $namespace = null;
    public string $prefix = '';
    public int $depth = 0;

    /** @var ElementNode[] */
    private array $childNodes = [];
    /** @var AttributeNode[] */
    private array $attributes = [];

    public function __construct(string $name, array $attributes = [])
    {
        $this->name = $name;
        $this->localName = $name;

        $match = [];
        if (preg_match('~^(?<namespace>.+)\:(?<localName>.+)$~', $name, $match)) {
            $this->namespace = $match['namespace'];
            $this->localName = $match['localName'];
        }

        array_map([$this, 'addAttribute'], $attributes);
    }

    public function appendChild(ElementNode $node): void
    {
        $this->childNodes[] = $node;
    }

    public function addAttribute(AttributeNode $node): void
    {
        if ($node instanceof XmlNamespace) {
            $node->declared = true;
            array_unshift($this->attributes, $node);
        } else {
            $this->attributes[] = $node;
        }
    }

    public function namespaces(): array
    {
        return array_filter($this->attributes, fn (AttributeNode $node) => $node instanceof XmlNamespace);
    }

    public function depth(): int
    {
        return $this->depth;
    }

    public function __toString(): string
    {
        $name = ($this->prefix ? $this->prefix . ':' : '') . $this->localName;

        $attributes = implode(' ', array_map('strval', $this->attributes));
        $contents = implode('', array_map('strval', $this->childNodes));
        if (!$contents) {
            return sprintf('<%s/>', trim($name . ' ' . $attributes));
        }

        return sprintf('<%s>%s</%s>', trim($name . ' ' . $attributes), $contents, $name);
    }
}
