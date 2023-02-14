<?php

namespace DMT\Test\XmlParser\Node;

use DMT\XmlParser\Node\XmlNamespace;
use DMT\XmlParser\Node\XmlNamespaces;
use PHPUnit\Framework\TestCase;

class XmlNamespacesTest extends TestCase
{
    public function testPop(): void
    {
        $namespaces = new XmlNamespaces([
            new XmlNamespace('http://example.org/ns', ''),
            new XmlNamespace('http://example.org/ns1', 'ns1'),
            new XmlNamespace('http://example.org/ns2', 'ns2'),
            $last = new XmlNamespace('http://example.org/ns', 'ns'),
        ]);

        $this->assertSame($last, $namespaces->pop());
        $this->assertNotContains($last, $namespaces);
    }

    public function testPopEmpty(): void
    {
        $namespaces = new XmlNamespaces();
        $this->assertNull($namespaces->pop());
    }

    public function testFind(): void
    {
        $namespaces = new XmlNamespaces([
            new XmlNamespace('http://example.org/ns', ''),
            new XmlNamespace('http://example.org/ns1', 'ns1'),
            $find = new XmlNamespace('http://example.org/ns', 'ns'),
            new XmlNamespace('http://example.org/ns2', 'ns2'),
        ]);

        $this->assertSame($find, $namespaces->find('http://example.org/ns'));
    }

    public function testNotFound()
    {
        $namespaces = new XmlNamespaces([
            new XmlNamespace('http://example.org/ns1', 'ns1'),
            new XmlNamespace('http://example.org/ns2', 'ns2'),
        ]);

        $this->assertNull($namespaces->find('http://example.org/ns'));
    }
}
