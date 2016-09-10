<?php

namespace Mufuphlex\LinalPrimitives;

class Vector extends Matrix
{
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
        return parent::set($i, 0, $value);
    }

    public function getSize()
    {
        return count($this->data);
    }
}