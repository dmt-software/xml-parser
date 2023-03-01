<?php

namespace DMT\Test\XmlParser\Node;

use DMT\XmlParser\Node\Attribute;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    /**
     * @dataProvider provideAttribute
     */
    public function testAttribute(Attribute $attribute, string $prefix, string $localName, string $asString): void
    {
        $attribute->prefix = $prefix;
        $namespace = preg_replace('~^(.*(?=\:))?(.*)$~', '$1', $attribute->name) ?: null;

        $this->assertSame($namespace, $attribute->namespace);
        $this->assertSame($localName, $attribute->localName);
        $this->assertSame($asString, strval($attribute));
    }

    public function provideAttribute(): iterable
    {
        return [
            'simple attribute' => [
                new Attribute('foo', 'bar'),
                '',
                'foo',
                'foo="bar"'
            ],
            'attribute with namespace without prefix' => [
                new Attribute('http://example.org/ns:foo', 'bar'),
                '',
                'foo',
                'foo="bar"'
            ],
            'attribute with ns-prefix ' => [
                new Attribute('http://example.org/ns:foo', 'bar'),
                'ns1',
                'foo',
                'ns1:foo="bar"'
            ],
            'attribute with quote' => [
                new Attribute('foo', 'bar"'),
                '',
                'foo',
                'foo="bar&quot;"'
            ],
        ];
    }
}
