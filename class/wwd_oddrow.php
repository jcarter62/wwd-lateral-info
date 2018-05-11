<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

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