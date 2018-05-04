<?php

class wwd_oddrow
{
    private $row;
    private $class;

    /**
     * wwd_oddrow constructor.
     */
    public function __construct($class)
    {
        $this->row = 0;
        $this->class = $class;
    }

    public function getClass()
    {
        $this->row += 1;
        if ($this->row % 2 != 0) {
            return $this->class;
        } else {
            return '';
        }
    }
}