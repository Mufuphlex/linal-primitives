<?php

namespace Mufuphlex\Tests\LinalPrimitives;

use Mufuphlex\LinalPrimitives\Matrix;
use Mufuphlex\LinalPrimitives\Vector;

class MatrixTest extends \PHPUnit_Framework_TestCase
{
    private static $source = array(
        array(1, 2),
        array(3, 4),
        array(5, 6),
    );

    public function testFromArray()
    {
        $m = static::m();
        static::assertInstanceOf('\Mufuphlex\LinalPrimitives\Matrix', $m);
        static::assertSame(static::$source, $m->asArray());
    }

    public function testPlus()
    {
        $m1 = static::m();
        $m2 = static::m();
        $resultAssert = static::$source;

        foreach ($resultAssert as &$row) {
            foreach ($row as &$val) {
                $val *= 2;
            }
        }

        $result = $m1->plus($m2);

        static::assertInstanceOf('\Mufuphlex\LinalPrimitives\Matrix', $result);
        static::assertSame($resultAssert, $result->asArray());
    }

    public function testDivideScalar()
    {
        $m = static::m();
        $d = mt_rand(2,10);
        $resultAssert = static::$source;

        foreach ($resultAssert as &$row) {
            foreach ($row as &$val) {
                $val /= $d;
            }
        }
        unset($row);
        unset($val);

        $result = $m->divideScalar($d);

        static::assertInstanceOf('\Mufuphlex\LinalPrimitives\Matrix', $result);

        foreach ($result->asArray() as $rowNum => $row) {
            foreach ($row as $colNum => $val) {
                static::assertEquals($resultAssert[$rowNum][$colNum], $val, 1E-3);
            }
        }
    }

    public function testMultipleScalar()
    {
        $m = static::m();
        $d = mt_rand(2, 10);
        $resultAssert = static::$source;

        foreach ($resultAssert as &$row) {
            foreach ($row as &$val) {
                $val *= $d;
            }
        }
        unset($row);
        unset($val);

        $result = $m->multipleScalar($d);

        static::assertInstanceOf('\Mufuphlex\LinalPrimitives\Matrix', $result);

        foreach ($result->asArray() as $rowNum => $row) {
            foreach ($row as $colNum => $val) {
                static::assertEquals($resultAssert[$rowNum][$colNum], $val);
            }
        }
    }

    public function testMultipleVector()
    {
        $m = static::m();
        $a = mt_rand(2, 5);
        $b = mt_rand(2, 5);
        $v = Vector::fromArray(array($a, $b));
        $result = $m->multipleVector($v);
        static::assertInstanceOf('\Mufuphlex\LinalPrimitives\Matrix', $result);
        static::assertSame(3, $result->getRowsNum());
        static::assertSame(1, $result->getColsNum());
    }

    /**
     * @expectedException \Mufuphlex\LinalPrimitives\MatrixException
     * @expectedExceptionMessage Sizes inequality
     */
    public function testMultipleVectorFailsOnWrongSize()
    {
        $m = static::m();
        $v = Vector::fromArray(array(1));
        $m->multipleVector($v);
    }

    public function testMultipleMatrix()
    {
        $m = static::m();
        $m2 = Matrix::fromArray(array(array(1, 2), array(5, 6)));
        $result = $m->multipleMatrix($m2);
        static::assertInstanceOf('\Mufuphlex\LinalPrimitives\Matrix', $result);
        static::assertSame(3, $result->getRowsNum());
        static::assertSame(2, $result->getColsNum());
    }

    /**
     * @expectedException \Mufuphlex\LinalPrimitives\MatrixException
     * @expectedExceptionMessage Sizes inequality
     */
    public function testMultipleMatrixFailsOnWrongSize()
    {
        $m = static::m();
        $m2 = Matrix::fromArray(array(array(1, 2), array(5, 6), array(7, 8)));
        $m->multipleMatrix($m2);
    }

    /**
     * @dataProvider multipleDataProvider
     */
    public function testMultiple($arg, $instanceType)
    {
        $m = static::m();
        static::assertInstanceOf($instanceType, $m->multiple($arg));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The arg is not of any supported type
     */
    public function testMultipleFailsOnBadArgument()
    {
        $m = static::m();
        $m->multiple('xaba');
    }

    public function testTranspose()
    {
        $a = mt_rand(1, 5);
        $b = mt_rand(6, 10);
        $m = Matrix::fromArray(array(array($a, $b)));
        $mt = $m->transpose();
        static::assertInstanceOf('\Mufuphlex\LinalPrimitives\Matrix', $mt);
        static::assertSame(array(array($a), array($b)), $mt->asArray());
    }

    /**
     * @dataProvider isSquareDataProvider
     */
    public function testIsSquare(Matrix $m, $assert)
    {
        static::assertSame($assert, $m->isSquare());
    }

    private static function m()
    {
        return Matrix::fromArray(static::$source);
    }

    public function multipleDataProvider()
    {
        return array(
            array(1, '\Mufuphlex\LinalPrimitives\Matrix'),
            array(Vector::fromArray(array(1, 2)), '\Mufuphlex\LinalPrimitives\Vector'),
            array(Matrix::fromArray(array(array(1, 2), array(5, 6))), '\Mufuphlex\LinalPrimitives\Matrix'),
        );
    }

    public function isSquareDataProvider()
    {
        return array(
            array(Matrix::fromArray(array(array(1, 2), array(5, 6))), true),
            array(Matrix::fromArray(array(array(1, 2))), false),
            array(Matrix::fromArray(array(array(1), array(2), array(3))), false),
            array(new Matrix(0, 0), true),
        );
    }

    public function testGetRowAsArray()
    {
        $m = static::m();
        $row = $m->getRowAsArray(0);
        static::assertSame(array(1, 2), $row);
        $row = $m->getRowAsArray(2);
        static::assertSame(array(5, 6), $row);
    }

    public function testGetMaxFromColumn()
    {
        $m = static::m();
        static::assertSame(5, $m->getMaxFromColumn(0));
        static::assertSame(6, $m->getMaxFromColumn(1));
    }

    public function testGetMinFromColumn()
    {
        $m = static::m();
        static::assertSame(1, $m->getMinFromColumn(0));
        static::assertSame(2, $m->getMinFromColumn(1));
    }

    public function testGetAvgFromColumn()
    {
        $m = static::m();
        static::assertSame(3, $m->getAvgFromColumn(0));
        static::assertSame(4, $m->getAvgFromColumn(1));
    }
}