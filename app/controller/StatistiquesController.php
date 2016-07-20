<?php
/**
 * Created by PhpStorm.
 * User: corentin
 * Date: 19/07/16
 * Time: 21:20
 */

class StatistiquesController extends Controller {

    private $_joueurs;
    private $_equipes;
    private $_connexion;

    function __construct()
    {
        parent::__construct();
        $this->_url = '/Statistiques';
        $this->setTemplate('/statistiques.phtml');
        $this->_title = 'Statistiques';
        $this->_page = 'Statistiques';

        $this->_connexion = SQLite::getInstance();

        $_equipeCollection = new EquipeCollection();
        $this->_equipes = $_equipeCollection->load(['en_cours' => ['=', 1]]);

        $_joueurCollection = new JoueurCollection();
        $this->_joueurs = $_joueurCollection->load(['visible' => ['=', 1]]);
    }

    /**
     * @return array
     */
    public function getWinLoseSimpleDom($sort=false) {
        $_stat = [];
        $query_ext = "SELECT joueur.id, joueur.name nom, equipe.name equipe, COUNT(joueur.name) total, 
                                  COUNT(CASE 
                                          WHEN (
                                            score_1_1 > score_1_2 AND 
                                            (score_2_1 > score_2_2 OR score_3_1 > score_3_2) 
                                            OR (score_2_1 > score_2_2 AND score_3_1 > score_3_2)
                                            ) 
                                          THEN score_1_1 END
                                  ) sup 
                          FROM simple 
                          INNER JOIN joueur 
                          ON simple.joueur_1 = joueur.id 
                          INNER JOIN equipe 
                          ON equipe.id = joueur.team 
                          GROUP BY joueur.id";

        $stmt = $this->_connexion->prepareQuery($query_ext, []);
        $results = $stmt->execute();
        while ($result = $results->fetchArray(SQLITE3_ASSOC)) {
            $_stat[$result['id']]['nom'] = $result['nom'];
            $_stat[$result['id']]['equipe'] = $result['equipe'];
            $_stat[$result['id']]['win'] = $result['sup'];
            $_stat[$result['id']]['total'] = $result['total'];
            $_stat[$result['id']]['ratio'] = $result['sup']*100/$result['total'];
        }

        if($sort) {
            usort($_stat,
                function ($a, $b) {
                    return $a['ratio'] > $b['ratio'] ? -1 : 1;
                }
            );
        }

        return $_stat;
    }

    /**
     * @return array
     */
    public function getWinLoseSimpleExt($sort=false) {
        $_stat = [];
        $query_ext = "SELECT joueur.id, joueur.name nom, equipe.name equipe, COUNT(joueur.name) total, 
                                  COUNT(CASE 
                                          WHEN (
                                            score_1_2 > score_1_1 AND 
                                            (score_2_2 > score_2_1 OR score_3_2 > score_3_1) 
                                            OR (score_2_2 > score_2_1 AND score_3_2 > score_3_1)
                                            ) 
                                          THEN score_1_2 END
                                  ) sup 
                          FROM simple 
                          INNER JOIN joueur 
                          ON simple.joueur_2 = joueur.id 
                          INNER JOIN equipe 
                          ON equipe.id = joueur.team 
                          GROUP BY joueur.id";

        $stmt = $this->_connexion->prepareQuery($query_ext, []);
        $results = $stmt->execute();
        while ($result = $results->fetchArray(SQLITE3_ASSOC)) {
            $_stat[$result['id']]['nom'] = $result['nom'];
            $_stat[$result['id']]['equipe'] = $result['equipe'];
            $_stat[$result['id']]['win'] = $result['sup'];
            $_stat[$result['id']]['total'] = $result['total'];
            $_stat[$result['id']]['ratio'] = $result['sup']*100/$result['total'];
        }

        if($sort) {
            usort($_stat,
                function ($a, $b) {
                    return $a['ratio'] > $b['ratio'] ? -1 : 1;
                }
            );
        }

        return $_stat;
    }

    /**
     * @param $array_dom
     * @param $array_ext
     * @return array
     */
    public function getWinLoseSimpleAll($array_dom, $array_ext) {
        $_stat = [];
        foreach($array_dom as $key => $value) {
            if(array_key_exists($key, $array_ext)){
               $_stat[$key]['nom'] = $array_dom[$key]['nom'];
               $_stat[$key]['equipe'] = $array_dom[$key]['equipe'];
               $_stat[$key]['win'] = $array_dom[$key]['win'] + $array_ext[$key]['win'];
               $_stat[$key]['total'] = $array_dom[$key]['total'] + $array_ext[$key]['total'];
               $_stat[$key]['ratio'] = $_stat[$key]['win']*100/$_stat[$key]['total']; 
            }
        }

        usort($_stat,
            function($a, $b){
                return $a['ratio'] > $b['ratio'] ? -1 : 1;
            }
        );

        return $_stat;
    }


    public function getHighestPointSimple($sort=false) {
        $_stat=[];
        $query_dom = "SELECT joueur.id, joueur.name nom, equipe.name equipe, 
                        SUM(score_1_1)+SUM(score_2_1)+SUM(score_3_1) sum,
                        COUNT(joueur.name) occ,
                        COUNT(joueur.name) score1, 
                        COUNT(CASE WHEN (score_3_1 != '') THEN joueur.id END) score3
                  FROM simple
                  INNER JOIN joueur
                  ON joueur.id = simple.joueur_1
                  INNER JOIN equipe
                  ON equipe.id = joueur.team
                  GROUP BY joueur.id";
        $query_ext = "SELECT joueur.id, joueur.name nom, equipe.name equipe, 
                        SUM(score_1_2)+SUM(score_2_2)+SUM(score_3_2) sum,
                        COUNT(joueur.name) score1, 
                        COUNT(CASE WHEN (score_3_2 != '') THEN joueur.id END) score3 
                  FROM simple
                  INNER JOIN joueur
                  ON joueur.id = simple.joueur_2
                  INNER JOIN equipe
                  ON equipe.id = joueur.team
                  GROUP BY joueur.id";
        
        $stmt_dom = $this->_connexion->prepareQuery($query_dom, []);
        $results_dom = $stmt_dom->execute();
        $stmt_ext = $this->_connexion->prepareQuery($query_ext, []);
        $results_ext = $stmt_ext->execute();
        while ($result = $results_dom->fetchArray(SQLITE3_ASSOC)) {
            $_stat[$result['id']]['nom'] = $result['nom'];
            $_stat[$result['id']]['equipe'] = $result['equipe'];
            $_stat[$result['id']]['sum'] = $result['sum'];
            $_stat[$result['id']]['total'] = 2*$result['score1']+$result['score3'];
        }
        while ($result = $results_ext->fetchArray(SQLITE3_ASSOC)) {
            if(array_key_exists($result['id'], $_stat)) {
                $_stat[$result['id']]['sum'] += $result['sum'];
                $_stat[$result['id']]['total'] += 2*$result['score1']+$result['score3'];
                $_stat[$result['id']]['ratio'] = number_format($_stat[$result['id']]['sum'] / $_stat[$result['id']]['total'], 2);
            }
        }

        if($sort) {
            usort($_stat,
                function ($a, $b) {
                    return $a['ratio'] > $b['ratio'] ? -1 : 1;
                }
            );
        }

        return $_stat;
    }

}