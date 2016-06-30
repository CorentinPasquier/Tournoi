<?php

/**
 * Created by PhpStorm.
 * User: matpif
 * Date: 12/04/16
 * Time: 19:35
 */
class EquipeCollection extends Collection
{

    function __construct() {
        parent::__construct();
        $this->_table = 'equipe';
        $this->_model = 'EquipeModel';
        $this->_key = 'id';
    }
    
    public function getNumberMatch() {
        $equipes = $this->load(['en_cours' => ['=', 1]]);
        return $equipes->count() - 1;
    }
}