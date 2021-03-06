<?php

namespace Mufuphlex\LinalPrimitives;

/**
 * Class Vector
 * @package Mufuphlex\LinalPrimitives
 */
class Vector extends Matrix
{
    /**
     * @param array $data
     * @return Vector
     */
    public static function fromArray(array $data)
    {
        $that = new static(count($data));

        foreach (array_values($data) as $i => $val) {
            $that->data[$i][0] = $val;
        }

        return $that;
    }

    public function __construct($size)
    {
        parent::__construct($size, 1);
    }

    /**
     * @param int $i
     * @return number
     */
    public function get($i)
    {
        return parent::get($i, 0);
    }

    /**
     * @param int $i
     * @param int $value
     * @return $this
     */
    public function set($i, $value)
    {
        parent::set($i, 0, $value);
        return $this;
    }

    /**
     * @param mixed $val
     * @return Vector
     */
    public function prepend($val)
    {
        $vectorSize = $this->getSize();
        $vals = array_fill(0, $vectorSize + 1, $val);

        for ($i = 0; $i < $vectorSize; $i++) {
            $vals[$i+1] = $this->get($i);
        }

        return Vector::fromArray($vals);
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->getRowsNum();
    }

    /**
     * @return array
     */
    public function asFlatArray()
    {
        $result = array_fill(0, count($this->data), null);

        foreach ($this->data as $i => $row) {
            $result[$i] = current($row);
        }

        return $result;
    }

    /**
     * @param callable $f
     * @return Vector
     */
    public function apply(Callable $f)
    {
        $result = clone $this;
        $size = $this->getSize();

        for ($i = 0; $i < $size; $i++) {
            $result->set($i, $f($this->data[$i][0]));
        }

        return $result;
    }
}