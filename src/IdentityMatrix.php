<?php

namespace Mufuphlex\LinalPrimitives;

class IdentityMatrix extends SquareMatrix
{
    public function __construct($size)
    {
        parent::__construct($size, 0);

        for ($i = 0; $i < $size; $i++) {
            $this->data[$i][$i] = 1;
        }
    }
}