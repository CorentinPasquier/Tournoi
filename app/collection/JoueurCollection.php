<?php

/**
 * Created by PhpStorm.
 * User: corentin
 * Date: 16/07/16
 * Time: 11:09
 */
class JoueurCollection extends Collection
{

    function __construct() {
        parent::__construct();
        $this->_table = 'joueur';
        $this->_model = 'JoueurModel';
        $this->_key = 'id';
    }
    
}