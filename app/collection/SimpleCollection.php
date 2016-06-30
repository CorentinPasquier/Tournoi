<?php
/**
 * Created by PhpStorm.
 * User: corentin
 * Date: 14/06/16
 * Time: 21:32
 */

class SimpleCollection extends Collection
{
    function __construct()
    {
        parent::__construct();
        $this->_table = 'simple';
        $this->_model = 'SimpleModel';
        $this->_key = 'id';
    }
}