<?php

namespace Mufuphlex\Tests\LinalPrimitives;

use Mufuphlex\LinalPrimitives\SquareMatrix;

class SquareMatrixTest extends \PHPUnit_Framework_TestCase
{
    public function testSize()
    {
        $size = mt_rand(2, 9);
        $m = new SquareMatrix($size);
        static::assertSame($size, $m->getRowsNum());
        static::assertSame($size, $m->getColsNum());
    }
}