<?php

/**
 * Created by PhpStorm.
 * User: matpif
 * Date: 29/04/16
 * Time: 15:45
 */
class MatchsController extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->_url = '/Matchs';
        $this->setTemplate('/matchs.phtml');
        $this->_title = 'Les Matchs';
        $this->_page = 'Matchs';
        $this->addJS('/js/detailMatchs.js');
    }

    /**
     * @return MatchCollection
     */
    public function getAllMatch() {
        $matchCollection = new MatchCollection();
        return $matchCollection->load(["en_cours" => ["<>", 0]],["ronde" => Collection::SORT_ASC]);
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
            if ($simple->getAttribute('score_1_1') > $simple->getAttribute('score_1_2'))
                $score++;
            else
                $score--;
            if ($simple->getAttribute('score_2_1') > $simple->getAttribute('score_2_2'))
                $score++;
            else
                $score--;
            if ($score == 0){
                if ($simple->getAttribute('score_3_1') > $simple->getAttribute('score_3_2'))
                    $score++;
                else
                    $score--;
            }
            if($score > 0)
                $score1++;
            else
                $score2++;
            $score = 0;
        }
        /** @var DoubleModel $double */
        foreach ($_allDouble as $double) {
            $flag++;
            if ($double->getAttribute('score_1_1') > $double->getAttribute('score_1_2'))
                $score++;
            else
                $score--;
            if ($double->getAttribute('score_2_1') > $double->getAttribute('score_2_2'))
                $score++;
            else
                $score--;
            if ($score == 0){
                if ($double->getAttribute('score_3_1') > $double->getAttribute('score_3_2'))
                    $score++;
                else
                    $score--;
            }
            if($score > 0)
                $score1++;
            else
                $score2++;
            $score = 0;
        }
        if($flag == 0){
            return ['', ''];
        }
        return [$score1, $score2];
    }

    /**
     * @return UtilisateurModel
     */
    public function getUtilisateur() {
        return Access::getInstance()->getCurrentUser();
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
     * Save les feuilles de matchs
     */
    public function saveDetailAction(){
        $post = Access::getRequest();
        $erreur = false;
        $array = array();
        $idMatch = $post['match'];
        $_simpleCollection = new SimpleCollection();
        $_doubleCollection = new DoubleCollection();
        $_allSimple = $_simpleCollection->loadArrayId(['match' => ['=', $idMatch]]);
        $_allDouble = $_doubleCollection->loadArrayId(['match' => ['=', $idMatch]]);
        foreach ($post['joueurS'] as $id => $joueur) {
            $simple = new SimpleModel();
            if(isset($post['databaseIdS'][$id])) {
                $simple->setAttribute('id', $post['databaseIdS'][$id]);
                if(($key = array_search($post['databaseIdS'][$id], $_allSimple)) !== false) {
                    unset($_allSimple[$key]);
                }

            }
            $simple->setAttribute('match', $post['match']);
            $simple->setAttribute('joueur_1', $joueur['joueur_1']);
            $simple->setAttribute('joueur_2', $joueur['joueur_2']);
            $array[$id] = $simple;
        }
        foreach ($post['scoreS'] as $id => $score) {
            if($score['match_1_1'] != "" && $score['match_1_2'] != "" && $score['match_2_1'] != "" && $score['match_2_2'] != "") {
                $array[$id]->setAttribute('score_1_1', $score['match_1_1']);
                $array[$id]->setAttribute('score_1_2', $score['match_1_2']);
                $array[$id]->setAttribute('score_2_1', $score['match_2_1']);
                $array[$id]->setAttribute('score_2_2', $score['match_2_2']);
                $array[$id]->setAttribute('score_3_1', $score['match_3_1']);
                $array[$id]->setAttribute('score_3_2', $score['match_3_2']);
                if (!$array[$id]->save())
                    $erreur = true;
            }
        }
        foreach ($_allSimple as $key=> $id) {
            $simple = $_simpleCollection->loadById($id);
            $simple->remove();
        }
        foreach ($post['joueurD'] as $id => $joueur) {
            $double = new DoubleModel();
            if(isset($post['databaseIdD'][$id])) {
                $double->setAttribute('id', $post['databaseIdD'][$id]);
                if(($key = array_search($post['databaseIdD'][$id], $_allDouble)) !== false) {
                    unset($_allDouble[$key]);
                }

            }
            $double->setAttribute('match', $post['match']);
            $double->setAttribute('joueur_1_1', $joueur['joueur_1_1']);
            $double->setAttribute('joueur_1_2', $joueur['joueur_1_2']);
            $double->setAttribute('joueur_2_1', $joueur['joueur_2_1']);
            $double->setAttribute('joueur_2_2', $joueur['joueur_2_2']);
            $array[$id] = $double;
        }
        foreach ($post['scoreD'] as $id => $score) {
            if($score['match_1_1'] != "" && $score['match_1_2'] != "" && $score['match_2_1'] != "" && $score['match_2_2'] != "") {
                $array[$id]->setAttribute('score_1_1', $score['match_1_1']);
                $array[$id]->setAttribute('score_1_2', $score['match_1_2']);
                $array[$id]->setAttribute('score_2_1', $score['match_2_1']);
                $array[$id]->setAttribute('score_2_2', $score['match_2_2']);
                $array[$id]->setAttribute('score_3_1', $score['match_3_1']);
                $array[$id]->setAttribute('score_3_2', $score['match_3_2']);
                if (!$array[$id]->save())
                    $erreur = true;
            }
        }
        foreach ($_allDouble as $key => $id) {
            $double = $_doubleCollection->loadById($id);
            $double->remove();
        }
        if (!$erreur) {
            $messages = new MessageManager();
            $messages->newMessage('Vos matchs ont été sauvegardés correctement', Message::LEVEL_SUCCESS);
        } else {
            $messages = new MessageManager();
            $messages->newMessage('Un problème est survenu, tous vos matchs n\'ont pas été enregistrés correctement.', Message::LEVEL_ERROR);
        }
    }
}