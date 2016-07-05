<?php

/**
 * Created by PhpStorm.
 * User: matpif
 * Date: 29/04/16
 * Time: 15:45
 */
class ClassementController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->_url = '/Classement';
        $this->_title = 'Classement';
        $this->_page = 'Classement';
        $this->setTemplate('/classement.phtml');
    }

    /**
     * @return UtilisateurCollection
     */
    private function getAllUtilisateur() {
        $utilisateurCollection = new UtilisateurCollection();
        return $utilisateurCollection->loadAll();
    }

    private function getAllEquipeEnCours() {
        $equipeCollection = new EquipeCollection();
        return $equipeCollection->load(['id' => ['<>', 0], 'en_cours' => ['=', 1]]);
    }

    /**
     * @param $utilisateurId
     * @return int
     */
    private function getScoreUtilisateur($utilisateurId) {
        $pariCollection = new PariCollection();
        $pariCollection->load(['utilisateur_id' => $utilisateurId]);

        $score = 0;
        /** @var PariModel $pari */
        foreach ($pariCollection as $pari) {
            $score += $pari->getScore();
        }

        return $score;
    }

    /**
     * @param $equipeId
     * @return int
     */
    private function getScoreEquipe($equipeId) {
        $matchCollection = new MatchCollection();
        $matchs = $matchCollection->load(['en_cours' => ['<>', 0]]);
        $matchController = new MatchsController();
        $score = 0;
        $joue = 0;
        /** @var MatchModel $match */
        foreach ($matchs as $match) {
            if($match->getAttribute('equipe_id_1') == $equipeId)
                $equipe = 1;
            else if ($match->getAttribute('equipe_id_2') == $equipeId)
                $equipe = 2;
            else
                $equipe = 0;
            if($equipe != 0) {
                $scores = $matchController->getScore($match);
                if($scores[0] != '' || $scores[1] != '') {
                    if ($equipe == 1) {
                        if ($scores[0] > $scores[1]) {
                            $score++;
                        }
                        $joue++;
                    } else if ($equipe == 2) {
                        if ($scores[1] > $scores[0]) {
                            $score++;
                        }
                        $joue++;
                    }
                }
            }
        }
        return ["score" => $score, "joue" => $joue];
    }

    /**
     * @return array
     */
    public function getAllEquipeWithScore() {
        $equipeCollection = $this->getAllEquipeEnCours();
        $array_score = [];
        /** @var EquipeModel $equipe */
        foreach ($equipeCollection as $equipe){
            $score = $this->getScoreEquipe($equipe->getAttribute('id'));
            $array_score[$equipe->getAttribute('name')] = $score;
        }
        foreach ($array_score as $key => $row) {
            $return_score[$key]  = $row['score'];
            $return_joue[$key] = $row['joue'];
        }
        array_multisort($return_score, SORT_DESC, $return_joue, SORT_ASC, $array_score);
        return $array_score;
    }

}