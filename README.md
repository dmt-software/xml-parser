# XML Parser

## Usage

```php
use DMT\XmlParser\Parser;
use DMT\XmlParser\Source\StringParser;
use DMT\XmlParser\Tokenizer;
 
$xml = '<books>
    <book><title lang="en_US">A book title</title></book>
    <book><title lang="en_US">An other book title</title></book>
<books>';

$parser = new Parser(new Tokenizer(new StringParser($xml));
while ($node = $parser->parse()) {
    // iterates: node <books>, node <book>, node <title>, text-node "A book title", node <book> etc
}
```
