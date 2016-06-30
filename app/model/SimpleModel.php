<?php

/**
 * Created by PhpStorm.
 * User: corentin
 * Date: 14/06/16
 * Time: 21:30
 */
class SimpleModel extends Model
{
    function __construct() {
        parent::__construct();
        $this->_key = 'id';
        $this->_table = 'simple';
    }
}