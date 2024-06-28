<?php

namespace DMT\XmlParser\Source;

interface Parser
{
    /**
     * Parse the source into characters.
     *
     * @return iterable
     * @throws \RuntimeException
     */
    public function parse(): iterable;

    /**
     * Get the metadata for the inner stream.
     *
     * @return array
     */
    public function getMetadata(): array;
}
