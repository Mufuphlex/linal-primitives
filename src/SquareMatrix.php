<?php

namespace Mufuphlex\LinalPrimitives;

class SquareMatrix extends Matrix
{
    public function __construct($size, $defaultValue = null)
    {
        parent::__construct($size, $size, $defaultValue);
    }
}