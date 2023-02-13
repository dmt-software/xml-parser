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
}