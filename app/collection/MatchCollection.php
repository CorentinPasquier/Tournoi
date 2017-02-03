<?php

/**
 * Created by PhpStorm.
 * User: matpif
 * Date: 12/04/16
 * Time: 19:35
 */
class MatchCollection extends Collection
{

    function __construct() {
        parent::__construct();
        $this->_table = 'match';
        $this->_model = 'MatchModel';
        $this->_key = 'id';
    }

    /**
     * Load Models by attributes values
     * @param $attributes
     * @param null|array $sort
     * @return Collection
     */
    public function loadOneTeam($attributes, $attributesOr, $sort = null) {
        $dataParamList = $this->_db->dataParamList($attributes, $this->_key, ' AND ', true);
        $dataParamList2 = $this->_db->dataParamList($attributesOr, $this->_key, ' OR ', true);
        $query = "SELECT * FROM {$this->_table} WHERE ".$dataParamList." AND (".$dataParamList2.")";
        if (is_array($sort)) {
            foreach ($sort as $key => $value) {
                $query .= " ORDER BY ".$key.' '.$value.',';
            }
            $query = substr($query, 0, -1);
        }
        $stmt = $this->_db->prepareQuery($query, array_merge($attributes, $attributesOr));
        $results = $stmt->execute();
        unset($this->_rows);

        while ($result = $results->fetchArray(SQLITE3_ASSOC)) {
            /**
             * @var $model Model
             */
            $model = new $this->_model;
            $model->setData($result);
            $this->_rows[] = $model;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentDay() {
        $query = "SELECT ronde
                  FROM {$this->_table}
                  WHERE id=MAX((SELECT MAX(`match`) FROM simple WHERE score_1_1 IS NOT NULL)
                              ,(SELECT MAX(`match`) FROM `double` WHERE score_1_1 IS NOT NULL))";
        $stmt = $this->_db->prepare($query);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC)["ronde"];
    }
        
}