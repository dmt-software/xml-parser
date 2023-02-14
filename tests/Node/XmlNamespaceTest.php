<?php

namespace DMT\Test\XmlParser\Node;

use DMT\XmlParser\Node\Attribute;
use DMT\XmlParser\Node\Element;
use DMT\XmlParser\Node\Node;
use DMT\XmlParser\Node\XmlNamespace;
use PHPUnit\Framework\TestCase;

class XmlNamespaceTest extends TestCase
{
    /**
     * @dataProvider provideNamespace
     */
    public function testNamespace(XmlNamespace $namespace, string $asString): void
    {
        $this->assertSame($asString, strval($namespace));
    }

    /**
     * @dataProvider provideNode
     */
    public function testPrefixNodeName(Node $node, string $asString)
    {
        $namespace = new XmlNamespace('http://example.org/ns', 'ns1');
        $namespace->prefixNodeName($node);

        if ($namespace->uri === ($node->namespace ?? null)) {
            $this->assertSame('ns1', $node->prefix ?? null);
        }
        $this->assertSame($asString, strval($node));
    }

    public function provideNamespace(): iterable
    {
        return [
            'namespace without prefix' => [
                new XmlNamespace('http://example.org/ns', ''),
                'xmlns="http://example.org/ns"'
            ],
            'namespace with prefix' => [
                new XmlNamespace('http://example.org/ns', 'ns1'),
                'xmlns:ns1="http://example.org/ns"'
            ],
        ];
    }

    public function provideNode(): iterable
    {
        return [
            'element with same namespace' => [
                new Element('http://example.org/ns:foo'),
                '<ns1:foo/>'
            ],
            'attribute with same namespace' => [
                new Attribute('http://example.org/ns:foo', 'bar'),
                'ns1:foo="bar"'
            ],
            'element without namespace' => [
                new Element('foo'),
                '<foo/>'
            ],
            'element with different namespace' => [
                new Element('urn-ns:foo'),
                '<foo/>'
            ],
            'attribute without namespace' => [
                new Attribute('foo', 'bar'),
                'foo="bar"'
            ],
            'attribute with different namespace' => [
                new Attribute('urn-ns:foo', 'bar'),
                'foo="bar"'
            ],
        ];
    }
}
