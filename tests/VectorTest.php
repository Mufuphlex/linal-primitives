<?php

namespace Mufuphlex\Tests\LinalPrimitives;

use Mufuphlex\LinalPrimitives\Vector;

class VectorTest extends \PHPUnit_Framework_TestCase
{
    public function testAsFlatArray()
    {
        $a = array(1, 2, 3);
        $v = Vector::fromArray($a);
        static::assertSame($a, $v->asFlatArray());
    }

    public function testPrepend()
    {
        $a = array(1, 2, 3);
        $v = Vector::fromArray($a);
        $v = $v->prepend(4);
        static::assertSame(array(4, 1, 2, 3), $v->asFlatArray());
    }
}