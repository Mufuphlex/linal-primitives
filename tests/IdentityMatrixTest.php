<?php

namespace Mufuphlex\Tests\LinalPrimitives;

use Mufuphlex\LinalPrimitives\IdentityMatrix;

class IdentityMatrixTest extends \PHPUnit_Framework_TestCase
{
    public function testValues()
    {
        $size = mt_rand(2, 9);
        $m = new IdentityMatrix($size);

        for ($i = 0; $i < $size; $i++) {
            static::assertSame(1, $m->get($i, $i));
        }
    }
}