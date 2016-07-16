<?php

/**
 * Created by PhpStorm.
 * User: corentin
 * Date: 16/07/16
 * Time: 11:09
 */
class JoueurModel extends Model
{
    
    function __construct() {
        parent::__construct();
        $this->_key = 'id';
        $this->_table = 'joueur';
    }
    
}