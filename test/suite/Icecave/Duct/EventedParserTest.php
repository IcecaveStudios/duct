<?php
namespace Icecave\Duct;

use Phake;
use PHPUnit_Framework_TestCase;

class EventedParserTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->tokenStreamParser = Phake::partialMock(__NAMESPACE__ . '\Detail\TokenStreamParser');
        $this->parser = new EventedParser(null, $this->tokenStreamParser);
    }

    public function testOn()
    {
        $callback = function() {};

        $this->parser->on('foo', $callback);

        Phake::verify($this->tokenStreamParser)->on('foo', $this->identicalTo($callback));
    }

    public function testOnce()
    {
        $callback = function() {};

        $this->parser->once('foo', $callback);

        Phake::verify($this->tokenStreamParser)->once('foo', $this->identicalTo($callback));
    }

    public function testRemoveListener()
    {
        $callback = function() {};

        $this->parser->removeListener('foo', $callback);

        Phake::verify($this->tokenStreamParser)->removeListener('foo', $this->identicalTo($callback));
    }

    public function testRemoveAllListeners()
    {
        $this->parser->removeAllListeners('foo');

        Phake::verify($this->tokenStreamParser)->removeAllListeners('foo');
    }

    public function testRemoveAllListenersAllEvents()
    {
        $this->parser->removeAllListeners();

        Phake::verify($this->tokenStreamParser)->removeAllListeners(null);
    }

    public function testListeners()
    {
        $callback = function() {};

        $this->parser->on('foo', $callback);

        $result = $this->parser->listeners('foo');

        $this->assertSame(array($callback), $result);
    }

    public function testEmit()
    {
        $this->parser->emit('foo', array(1, 2, 3));

        Phake::verify($this->tokenStreamParser)->emit('foo', array(1, 2, 3));
    }
}