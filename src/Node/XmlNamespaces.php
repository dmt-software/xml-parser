<?php

namespace DMT\XmlParser\Node;

use ArrayObject;

class XmlNamespaces extends ArrayObject
{
    public function find(string $namespace): ?XmlNamespace
    {
        $count = count($this) - 1;
        for ($i = $count; $i >= 0; $i--) {
            $node = $this->offsetGet($i);
            if ($node->uri == $namespace) {
                return $node;
            }
        }

        return null;
    }

    public function pop(): ?XmlNamespace
    {
        $copy = $this->getArrayCopy();
        $namespace = array_pop($copy);

        $this->exchangeArray($copy);

        return $namespace ?: null;
    }
}
