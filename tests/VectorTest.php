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

    public function testApply()
    {
        $a = array(1, 2, 3);
        $v = Vector::fromArray($a);

        $v = $v->apply(function($val){
            return $val+1;
        });

        static::assertSame(array(2, 3, 4), $v->asFlatArray());
    }
}