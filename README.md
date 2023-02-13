# XML Parser

## Usage

```php
$xml = '<books>
    <book><title lang="en_US">A book title</title></book>
    <book><title lang="en_US">An other book title</title></book>
<books>';

$parser = new Parser(new Tokenizer(new StringParser($xml));
while ($node = $parser->parse()) {
    // iterates: node <books>, node <book>, node <title>, text-node "A book title", etc
}
```

```php
$xml = '<books>
    <book><title lang="en_US">A book title</title></book>
    <book><title lang="en_US">An other book title</title></book>
<books>';

$parser = new Parser(new Tokenizer(new StringParser($xml));
while ($node = $parser->parse()) {
    if ($node->localName == 'title') {
        die($parser->parseXml());
    }
}
// outputs <title lang="en_US">A book title</title>
```