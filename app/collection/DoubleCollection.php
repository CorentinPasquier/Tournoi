<?php
/**
 * Created by PhpStorm.
 * User: corentin
 * Date: 14/06/16
 * Time: 21:32
 */

class DoubleCollection extends Collection
{
    function __construct()
    {
        parent::__construct();
        $this->_table = 'double';
        $this->_model = 'DoubleModel';
        $this->_key = 'id';
    }
}