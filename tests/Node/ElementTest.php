<?php

namespace DMT\Test\XmlParser\Node;

use DMT\XmlParser\Node\Attribute;
use DMT\XmlParser\Node\Element;
use DMT\XmlParser\Node\Text;
use DMT\XmlParser\Node\XmlNamespace;
use PHPUnit\Framework\TestCase;

class ElementTest extends TestCase
{
    /**
     * @dataProvider provideElement
     */
    public function testElement(Element $element, string $prefix, string $localName, string $asString): void
    {
        $element->prefix = $prefix;
        $namespace = preg_replace('~^(.*(?=\:))?(.*)$~', '$1', $element->name) ?: null;

        $this->assertSame($namespace, $element->namespace);
        $this->assertSame($localName, $element->localName);
        $this->assertSame($asString, strval($element));
    }

    public function testElementWithNamespaces(): void
    {
        $language = new Attribute('http://example.org/ns-1:lang', 'en_US');
        $language->prefix = 'ns1';

        $element = new Element('foo');
        $element->addAttribute($ns1 = new XmlNamespace('http://example.org/ns-1', 'ns1'));
        $element->addAttribute($ns = new XmlNamespace('http://example.org/ns', ''));
        $element->addAttribute($language);


        $this->assertCount(2, $element->namespaces());
        $this->assertContains($ns, $element->namespaces());
        $this->assertContains($ns1, $element->namespaces());

        $this->assertSame(
            '<foo xmlns="http://example.org/ns" xmlns:ns1="http://example.org/ns-1" ns1:lang="en_US"/>',
            strval($element)
        );
    }

    public function provideElement(): iterable
    {
        $elementWithTextNode = new Element('foo');
        $elementWithTextNode->appendChild(new Text('data'));

        $elementWithChildNode = new Element('foo');
        $elementWithChildNode->appendChild(new Element('bar'));

        $elementWithNamespace = new Element('foo');
        $elementWithNamespace->addAttribute(new XmlNamespace('http://example.org/ns', 'ns1'));

        return [
            'simple element' => [
                new Element('foo'),
                '',
                'foo',
                '<foo/>'
            ],
            'element with namespace without prefix' => [
                new Element('http://example.org/ns:foo'),
                '',
                'foo',
                '<foo/>'
            ],
            'element with namespace with prefix' => [
                new Element('http://example.org/ns:foo'),
                'ns1',
                'foo',
                '<ns1:foo/>'
            ],
            'element with attributes' => [
                new Element('foo', [new Attribute('lang', 'en_US'), new Attribute('type', 'text')]),
                '',
                'foo',
                '<foo lang="en_US" type="text"/>'
            ],
            'element with text content' => [
                $elementWithTextNode,
                '',
                'foo',
                '<foo>data</foo>'
            ],
            'element with child node' => [
                $elementWithChildNode,
                '',
                'foo',
                '<foo><bar/></foo>'
            ],
            'element with namespace attribute' => [
                $elementWithNamespace,
                'ns1',
                'foo',
                '<ns1:foo xmlns:ns1="http://example.org/ns"/>'
            ]
        ];
    }
}
