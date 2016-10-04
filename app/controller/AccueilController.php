<?php

/**
 * Created by PhpStorm.
 * User: matpif
 * Date: 25/04/16
 * Time: 13:52
 */
class AccueilController extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->_url = '/Accueil';
        $this->setTemplate('/accueil.phtml');
        $this->_title = 'Accueil';
        $this->_page = 'Accueil';
        $this->addJS('/js/accueil.js');
    }

    /**
     * @param int
     * @return MatchCollection
     */
    public function getAllMatchByTeam($id) {
        $matchCollection = new MatchCollection();
        return $matchCollection->loadOneTeam(["en_cours" => ["<>", 0]], ["equipe_id_1" => ["=", $id], "equipe_id_2" => ["=", $id]],["ronde" => Collection::SORT_ASC]);
    }

    /**
     * @return Collection
     */
    public function getAllEquipe() {
        $_equipeCollection = new EquipeCollection();
        return $_equipeCollection->load(["id" => ["<>", 0]],["name" => Collection::SORT_ASC]);
    }

    /**
     * Affiche la page des feuilles de match
     */
    public function detailAction(){
        $post = Access::getRequest();
        $this->_post = $post;
        $this->setTemplate('/detailMatch.phtml');
        $this->_title = 'Création de match';
    }

    /**
     * @param MatchModel $match
     * @return array
     */
    public function getScore($match){
        $score = 0;
        $score1 = 0;
        $score2 = 0;
        $flag = 0;
        $_simpleCollection = new SimpleCollection();
        $_doubleCollection = new DoubleCollection();
        $_allSimple = $_simpleCollection->load(["match" => ["=", $match->getAttribute('id')]]);
        $_allDouble = $_doubleCollection->load(["match" => ["=", $match->getAttribute('id')]]);
        /** @var SimpleModel $simple */
        foreach ($_allSimple as $simple) {
            $flag++;
            if($simple->getAttribute('score_1_1') != "" || $simple->getAttribute('score_1_2') != "") {
                if ($simple->getAttribute('score_1_1') > $simple->getAttribute('score_1_2'))
                    $score++;
                else
                    $score--;
                if ($simple->getAttribute('score_2_1') > $simple->getAttribute('score_2_2'))
                    $score++;
                else
                    $score--;
                if ($score == 0) {
                    if ($simple->getAttribute('score_3_1') > $simple->getAttribute('score_3_2'))
                        $score++;
                    else
                        $score--;
                }
                if ($score > 0)
                    $score1++;
                else
                    $score2++;
                $score = 0;
            }
        }
        /** @var DoubleModel $double */
        foreach ($_allDouble as $double) {
            $flag++;
            if($double->getAttribute('score_1_1') != "" && $double->getAttribute('score_1_2') != "") {
                if ($double->getAttribute('score_1_1') > $double->getAttribute('score_1_2'))
                    $score++;
                else
                    $score--;
                if ($double->getAttribute('score_2_1') > $double->getAttribute('score_2_2'))
                    $score++;
                else
                    $score--;
                if ($score == 0) {
                    if ($double->getAttribute('score_3_1') > $double->getAttribute('score_3_2'))
                        $score++;
                    else
                        $score--;
                }
                if ($score > 0)
                    $score1++;
                else
                    $score2++;
                $score = 0;
            }
        }
        if($flag == 0){
            return ['', ''];
        }
        return [$score1, $score2];
    }

    public function getInfoTeam($id){
        $_userCollection = new UtilisateurCollection();
        /** @var UtilisateurModel $user */
        $user = $_userCollection->load(['equipe_default' => $id])->getFirstRow();
        if($user != null):
            $info = $user->getAttribute('firstName') != null ? '<b>'.$user->getAttribute('firstName') : '';
            $info .= $user->getAttribute('lastName') != null ? ' '.$user->getAttribute('lastName').'</b>' : '';
            $info.= $user->getAttribute('telephone') != null ? "<br />".$user->getAttribute('telephone') : '';
            $info.= $user->getAttribute('email') != null ? "<br />".$user->getAttribute('email') : '';
        else:
            $info = '';
        endif;
        return $info;
    }
    
}