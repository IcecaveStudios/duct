# Duct

[![Build Status]](http://travis-ci.org/IcecaveStudios/duct)
[![Test Coverage]](http://icecavestudios.github.io/duct/artifacts/tests/coverage)

**Duct** is a PHP library for incrementally parsing continuous streams of JSON values.

**Duct** is designed to parse sequential JSON values from data streams, without framing or demarcation outside of the
JSON specification.

* Install via [Composer](http://getcomposer.org) package [icecave/duct](https://packagist.org/packages/icecave/duct)
* Read the [API documentation](http://icecavestudios.github.io/duct/artifacts/documentation/api/)

## Examples

### Simple parsing

**Duct** can be used to parse multiple JSON documents at a time using the `Parser::parse()` method.
The JSON string given must contain complete values.

```php
use Icecave\Duct\Parser;

$parser = new Parser;
$values = $parser->parse('[ 1, 2, 3 ] [ 4, 5, 6 ]');

assert($values[0] === array(1, 2, 3));
assert($values[1] === array(4, 5, 6));
```

### Incremental parsing

Asynchronous, incremental parsing is also possible using the `Parser::feed()`, `values()` and `finalize()` methods.

```php
use Icecave\Duct\Parser;

$parser = new Parser;

// JSON data can be fed to the parser incrementally.
$parser->feed('[ 1, ');

// Completed values can be retreived using the values() method, which returns an
// Icecave\Collections\Vector of values.
//
// At this point no complete object has been parsed so the vector is empty.
$values = $parser->values();
assert($values->isEmpty());

// As more data is fed to the parser, we now have one value available, an array
// of elements 1, 2, 3.
//
// Note that calling values() is destructive, in that any complete objects are
// removed from the parser and will not be returned by future calls to values().
$parser->feed('2, 3 ][ 4, 5');
$values = $parser->values();
assert($values->size() === 1);
assert($values[0] == array(1, 2, 3));

// Finally we feed the remaining part of the second object to the parser and the
// second value becomes available.
$parser->feed(', 6 ]');
$values = $parser->values();
assert($values->size() === 1);
assert($values[0] == array(4, 5, 6));

// At the end of the JSON stream, finalize is called to parse any data remaining
// in the buffer.
$parser->finalize();

// In this case there were no additional values.
$values = $parser->values();
assert($values->isEmpty());
```

### Event-based parsing

**Duct** also provides `EventedParser`, an event-based incremental parser similar to the [Clarinet](https://github.com/dscape/clarinet) library for JavaScript.
The evented parse uses [Evenement](https://github.com/igorw/evenement) for event management.

As per the example above the `feed()` and `finalize()` methods are used, however there is no `values()` method. Instead,
the following events are emitted as the buffer is parsed.

 * **array-open**: emitted when an object open bracket is encountered
 * **array-close**: emitted when an object closing bracket is encountered
 * **object-open**: emitted when an object open brace is encountered
 * **object-close**: emitted when an object closing brace is encountered
 * **object-key** (string $key): emitted when an object key is encountered
 * **value** (mixed $value): emitted whenever a scalar or null is encountered, including inside objects and arrays
 * **document** (mixed $value): emitted after an entire JSON document has been parsed

<!-- references -->
[Build Status]: https://raw.github.com/IcecaveStudios/duct/gh-pages/artifacts/images/icecave/regular/build-status.png
[Test Coverage]: https://raw.github.com/IcecaveStudios/duct/gh-pages/artifacts/images/icecave/regular/coverage.png
