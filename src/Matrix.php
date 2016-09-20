<?php

namespace Mufuphlex\LinalPrimitives;

/**
 * Class Matrix
 * @package Mufuphlex\LinalPrimitives
 */
class Matrix
{
    /** @var array */
    protected $data = array();

    /** @var array */
    protected $columns = array();

    /**
     * @param array $data
     * @return Matrix
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

    public function __clone()
    {
        $this->resetColumns();
    }

    /**
     * @param int $i
     * @param int $j
     * @return number
     */
    public function get($i, $j)
    {
        return $this->data[$i][$j];
    }

    /**
     * @param int $i
     * @param int $j
     * @param number $value
     * @return $this
     */
    public function set($i, $j, $value)
    {
        $this->resetColumns();
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
     * @param number $value
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
     * @throws MatrixException
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
     * @param number $value
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

    /**
     * @param Vector $vector
     * @param int $colNum
     * @return $this
     */
    public function addColumn(Vector $vector, $colNum = 0)
    {
        $this->columns = array();

        foreach ($vector->asArray() as $i => $value) {
            $this->data[$i][$colNum] = $value;
        }

        return $this;
    }

    /**
     * @param int $j
     * @return Vector
     */
    public function getColumn($j)
    {
        return Vector::fromArray($this->getColumnAsArray($j));
    }

    /**
     * @param int $i
     * @return array
     */
    public function getRowAsArray($i)
    {
        return $this->data[$i];
    }

    /**
     * @param int $j
     * @return array
     */
    public function getColumnAsArray($j)
    {
        if (!isset($this->columns[$j])) {
            $rowsNum = $this->getRowsNum();
            $this->columns[$j] = array_fill(0, $rowsNum, null);

            for ($i = 0; $i < $rowsNum; $i++) {
                $this->columns[$j][$i] = $this->data[$i][$j];
            }
        }

        return $this->columns[$j];
    }

    /**
     * @param void
     * @return bool
     */
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

    /**
     * @param void
     * @return Matrix
     */
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
     * @param int $j
     * @return number
     */
    public function getMaxFromColumn($j)
    {
        return max($this->getColumnAsArray($j));
    }

    /**
     * @param int $j
     * @return number
     */
    public function getMinFromColumn($j)
    {
        return min($this->getColumnAsArray($j));
    }

    /**
     * @param int $j
     * @return number
     */
    public function getAvgFromColumn($j)
    {
        $column = $this->getColumnAsArray($j);
        return array_sum($column) / count($column);
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->data;
    }

    /**
     * @param void
     * @return $this
     */
    protected function resetColumns()
    {
        $this->columns = array();
        return $this;
    }
}