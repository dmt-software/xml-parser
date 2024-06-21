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
     * Get the inner stream.
     *
     * @return resource
     */
    public function getStream();
}
