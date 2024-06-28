<?php

namespace DMT\XmlParser;

use DMT\XmlParser\Node\XmlNamespaces;
use Generator;

interface Tokenizer
{
    public const XML_DROP_NAMESPACES = 1;
    public const XML_USE_CDATA = 2;

    public function namespaces(): XmlNamespaces;
    public function tokenize(): Generator;
}
