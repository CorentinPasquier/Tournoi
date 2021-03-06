<?php

/**
 * Created by PhpStorm.
 * User: matpif
 * Date: 12/04/16
 * Time: 18:41
 */
class MatchModel extends Model
{

    function __construct() {
        parent::__construct();
        $this->_key = 'id';
        $this->_table = 'match';
    }

    /**
     * @return EquipeModel|null
     */
    public function getEquipe_1() {
        $equipeCollection = new EquipeCollection();
        return $equipeCollection->loadById($this->getAttribute('equipe_id_1'));
    }

    /**
     * @return EquipeModel|null
     */
    public function getEquipe_2() {
        $equipeCollection = new EquipeCollection();
        return $equipeCollection->loadById($this->getAttribute('equipe_id_2'));
    }
    
    /**
     * Get score by team name key
     * @param $key int
     * @return mixed|string
     */
    public function getScore($key) {
        if($key == 1){
            if (is_array($this->_data) && array_key_exists('score_equipe_1', $this->_data)){
                return $this->_data['score_equipe_1'];
            }
            else{
                return '-';
            }
        }
        elseif($key == 2){
            if (is_array($this->_data) && array_key_exists('score_equipe_2', $this->_data)){
                return $this->_data['score_equipe_2'];
            }
            else{
                return '-';
            }
        }
        else{
            return '-';
        }
    }

    /**
     * @return mixed
     */
    public function getDate() {
        $week = $this->getAttribute('date');
        if($week != null) {
            $year = date("Y");
            $readini = new ReadIni('../tools/config.ini');
            $current_week = $readini->getAttribute('info_tournoi', 'start_week');
            if($week < $current_week)
                $year++;
            
            $time = strtotime("1 January $year", time());
            $day = date('w', $time);
                if($day == 0)
                    $day = 7;
            $time += ((7 * $week) + 1 - $day) * 24 * 3600;
            $return[0] = date('d-m-Y', $time);
            $time += 13 * 24 * 3600;
            $return[1] = date('d-m-Y', $time);
        }
        else if ($week == 52) {
            $year = date("Y");
            $time = strtotime("1 January $year", time());
            $day = date('w', $time);
            if($day == 0)
                $day = 7;
            $time += ((7 * $week) + 1 - $day) * 24 * 3600;
            $return[0] = date('d-m-Y', $time);

            $year++;
            $time = strtotime("1 January $year", time());
            $day = date('w', $time);
            if($day == 0)
                $day = 7;
            $time += (8 - $day) * 24 * 3600;
            $return[1] = date('d-m-Y', $time);
        }
        else {
            $return[0] = "";
            $return[1] = "";
        }
        return $return;
    }
}
