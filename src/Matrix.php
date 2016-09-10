<?php

namespace Mufuphlex\LinalPrimitives;

class Matrix
{
    protected $data = array();

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data)
    {
        $that = new static(count($data), 0);
        $i = 0;

        foreach ($data as $row){
            $j = 0;

            foreach ($row as $val) {
                $that->data[$i][$j] = $val;
                $j++;
            }

            $i++;
        }

        return $that;
    }

    public function __construct($m, $n, $defaultValue = null)
    {
        for ($i = 0; $i < $m; $i++) {
            $this->data[] = array_fill(0, $n, $defaultValue);
        }
    }

    /**
     * @param int $i
     * @param int $j
     * @return numeric
     */
    public function get($i, $j)
    {
        return $this->data[$i][$j];
    }

    /**
     * @param int $i
     * @param int $j
     * @param numeric $value
     * @return $this
     */
    public function set($i, $j, $value)
    {
        $this->data[$i][$j] = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getRowsNum()
    {
        return count($this->data);
    }

    /**
     * @return int
     */
    public function getColsNum()
    {
        //$cur = current($this->data);
        if ($this->getRowsNum() === 0) {
            return 0;
        }
        return count($this->data[0]);
    }

    /**
     * @param Matrix $matrix
     * @return Matrix
     */
    public function plus(Matrix $matrix)
    {
        $result = clone $this;

        foreach ($this->data as $rowNum => $row) {
            foreach ($row as $colNum => $col) {
                $result->data[$rowNum][$colNum] += $matrix->get($rowNum, $colNum);
            }
        }

        return $result;
    }

    /**
     * @param numeric $value
     * @return Matrix
     */
    public function multipleScalar($value)
    {
        $result = clone $this;

        foreach ($this->data as $rowNum => $row) {
            foreach ($row as $colNum => $col) {
                $result->data[$rowNum][$colNum] *= $value;
            }
        }

        return $result;
    }

    /**
     * @param Vector $vector
     * @return Vector
     */
    public function multipleVector(Vector $vector)
    {
        if ($this->getColsNum() !== $vector->getSize()){
            throw new MatrixException('Sizes inequality');
        }

        $result = new Vector(count($this->data));

        foreach ($this->data as $rowNum => $row) {
            $val = 0;

            foreach ($row as $colNum => $col) {
                $val += $col * $vector->get($colNum);
            }

            $result->set($rowNum, $val);
        }

        return $result;
    }

    /**
     * @param Matrix $matrix
     * @return Matrix
     * @throws MatrixException
     */
    public function multipleMatrix(self $matrix)
    {
        if ($this->getColsNum() !== $matrix->getRowsNum()) {
            throw new MatrixException('Sizes inequality');
        }

        $newColsCnt = $matrix->getColsNum();
        $result = new self($this->getRowsNum(), $newColsCnt);

        for ($j = 0; $j < $newColsCnt; $j++) {
            $result->addColumn($this->multipleVector($matrix->getColumn($j)), $j);
        }

        return $result;
    }

    /**
     * @param mixed $arg
     * @return Matrix|Vector
     */
    public function multiple($arg)
    {
        if (is_numeric($arg)) {
            return $this->multipleScalar($arg);
        } elseif ($arg instanceof Vector) {
            return $this->multipleVector($arg);
        } elseif ($arg instanceof self) {
            return $this->multipleMatrix($arg);
        }

        throw new \InvalidArgumentException('The arg is not of any supported type');
    }

    /**
     * @param numeric $value
     * @return Matrix
     */
    public function divideScalar($value)
    {
        $result = clone $this;

        foreach ($this->data as $rowNum => $row) {
            foreach ($row as $colNum => $col) {
                $result->data[$rowNum][$colNum] /= $value;
            }
        }

        return $result;
    }

    public function addColumn(Vector $vector, $colNum = 0)
    {
        foreach ($vector->asArray() as $i => $value) {
            $this->data[$i][$colNum] = $value;
        }

        return $this;
    }

    public function getColumn($j)
    {
        $rowsNum = $this->getRowsNum();
        $vector = new Vector($rowsNum);

        for ($i = 0; $i < $rowsNum; $i++) {
            $vector->set($i, $this->data[$i][$j]);
        }

        return $vector;
    }

    public function isSquare()
    {
        return ($this->getRowsNum() === $this->getColsNum());
    }

    public function inverse()
    {
        if (!$this->isSquare()) {
            throw new MatrixException('Can not inverse not square matrix');
        }
    }

    public function transpose()
    {
        $result = new self($this->getColsNum(), $this->getRowsNum());

        foreach ($this->data as $rowNum => $row) {
            foreach ($row as $colNum => $col) {
                $result->data[$colNum][$rowNum] = $col;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->data;
    }
}