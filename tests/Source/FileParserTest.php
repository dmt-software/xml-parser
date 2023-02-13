<?php

namespace DMT\Test\XmlParser\Source;

use DMT\XmlParser\Source\FileParser;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FileParserTest extends TestCase
{
    /**
     * @dataProvider provideFile
     */
    public function testParse(string $file, int $length, string $expected): void
    {
        $parser = new FileParser($file, $length);

        $contents = '';
        foreach ($parser->parse() as $char) {
            $contents .= $char;
            $this->assertSame(1, strlen($char));
        }

        $this->assertSame($expected, $contents);
    }

    public function provideFile(): iterable
    {
        $file = __DIR__ . '/../fixtures/books.xml';
        $contents = file_get_contents($file);

        return [
            'relative path' => [$file, 1024, $contents],
            'full path' => [realpath($file), 512, $contents],
            'file wrapper' => ['file://' . realpath($file), 256, $contents],
        ];
    }

    public function testIllegalFormattedFile(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FileParser(__DIR__ . '/missing-file');
    }
}
