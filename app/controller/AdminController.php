<?php

/**
 * Created by PhpStorm.
 * User: matpif
 * Date: 04/05/16
 * Time: 16:52
 */
class AdminController extends Controller
{

    const DEFAULT_PRIVILEGE = UtilisateurModel::PRIVILEGE_USER;

    /**
     * @var UtilisateurModel
     */
    private $_newUser;

    function __construct()
    {
        parent::__construct();
        $this->_url = '/Admin';
        $this->setTemplate('/admin/index.phtml');
        $this->_title = 'Administration';
        $this->_page = 'Admin';
        $this->setTemplateHeader('/admin/header.phtml');
        $this->addJS('/js/generateTournament.js');
    }

    public function newUserAction() {
        $get = Access::getRequest();
        if (isset($get['id'])) {
            $this->_newUser = (new UtilisateurCollection())->loadById($get['id']);
        }

        $this->setTemplate('/admin/addUser.phtml');
        $this->_title = 'Création de profil';
    }

    public function newMatchAction() {
        $this->setTemplate('/admin/addMatch.phtml');
        $this->_title = 'Création de match';
    }

    public function newPouleAction() {
        $this->setTemplate('/admin/addPoule.phtml');
        $this->_title = 'Création de poule';
    }

    public function newEquipeAction() {
        $this->setTemplate('/admin/addEquipe.phtml');
        $this->_title = 'Création d\'équipe';
    }

    public function generateTournamentAction() {
        $this->setTemplate('/admin/generateTournament.phtml');
        $this->_title = 'Générer un tournoi';
    }

    public function listUserAction() {
        $this->setTemplate('/admin/listUser.phtml');
        $this->_title = 'Liste des utilisateurs';
    }

    public function listMatchAction() {
        $this->setTemplate('/admin/listMatch.phtml');
        $this->_title = 'Liste des matchs';
    }

    /** Créé un tournoi en fonction des équipes sélectionnées */
    public function createTournamentAction(){
        $post = Access::getRequest();
        $erreur = false;
        if(isset($post['equipes'])){
            $size = sizeof($post['equipes']);
            if($size % 2 == 1){
                array_push($post['equipes'], "0"); // Ajout de l'exempt si nombre équipe impair
                $size++;
            }
            shuffle($post['equipes']);
            $equipes = $this->getAllEquipe();
            /** @var EquipeModel $equipe */
            foreach ($equipes as $equipe) {
                if(in_array($equipe->getAttribute('id'), $post['equipes'])){
                    $equipe->setAttribute('en_cours', 1);
                }
                else{
                    $equipe->setAttribute('en_cours', 0);
                }
                if(!$equipe->save())
                    $erreur = true;
            }
            for($i = 0; $i < $size-1; $i++){ // Parcours par jour
                for($j = 0; $j < $size/2; $j++) {
                    $match = new MatchModel();
                    $match->setAttribute('ronde', $i+1);
                    if($i % 2 == 1) {
                        $match->setAttribute('equipe_id_1', $post['equipes'][$size - $j - 1]);
                        $match->setAttribute('equipe_id_2', $post['equipes'][$j]);
                    }
                    else{
                        $match->setAttribute('equipe_id_1', $post['equipes'][$j]);
                        $match->setAttribute('equipe_id_2', $post['equipes'][$size - $j - 1]);
                    }
                    $match->setAttribute('en_cours', 1);
                    if(!$match->save())
                        $erreur = true;
                }
                if($i != $size-2) {
                    $temp = $post['equipes'][$size - 1];
                    for ($k = $size - 1; $k > 1; $k--) {
                        $post['equipes'][$k] = $post['equipes'][$k - 1];
                    }
                    $post['equipes'][1] = $temp;
                }
            }
        }
        if($erreur){
            $message = new MessageManager();
            $message->newMessage('Erreur lors de la création du tournoi', Message::LEVEL_ERROR);
            $this->setTemplate('/admin/generateTournament.phtml');
        }
        else{
            $message = new MessageManager();
            $message->newMessage('Le tournoi a bien été créé !', Message::LEVEL_SUCCESS);
            $this->redirect($this->getUrlAction('generateTournament'));
        }
    }

    /** Termine le tournoi en cours */
    public function closeTournamentAction(){
        $erreur = false;
        $matchCollection = new MatchCollection();
        $all_Matchs = $matchCollection->load(['en_cours' => ['<>', 0]]);
        foreach ($all_Matchs as $match) {
            $match->setAttribute('en_cours', 0);
            if(!$match->save())
                $erreur = true;
        }
        if($erreur){
            $message = new MessageManager();
            $message->newMessage('Erreur lors de la cloture du tournoi', Message::LEVEL_ERROR);
            $this->setTemplate('/admin/generateTournament.phtml');
        }
        else{
            $message = new MessageManager();
            $message->newMessage('Le tounroi a bien été cloturé', Message::LEVEL_SUCCESS);
            $this->redirect($this->getUrlAction('generateTournament'));
        }
    }

    public function addUserAction(){
        $post = Access::getRequest();

        $utilisateur = new UtilisateurModel();

        if (isset($post['login'], $post['email'], $post['password'], $post['password2'])) {

            if (isset($post['id'])) {
                $utilisateur->setAttribute('id', $post['id']);
            }
            
            $utilisateur->setAttribute('login', $post['login']);
            $utilisateur->setAttribute('email', $post['email']);
            $utilisateur->setAttribute('lastName', $post['lastName']);
            $utilisateur->setAttribute('firstName', $post['firstName']);
            $utilisateur->setAttribute('equipe_default', $post['equipe']);
            $utilisateur->setAttribute('privilege', self::DEFAULT_PRIVILEGE);

            if (!empty($post['password'])
                && !empty($post['password2'])
                && $post['password'] == $post['password2']) {

                $utilisateur->setPassword($post['password']);
            } else {
                $this->_newUser = $utilisateur;
                $messages = new MessageManager();
                $messages->newMessage('Les mots de passe ne sont pas identiques', Message::LEVEL_ERROR);
                $this->setTemplate('/admin/addUser.phtml');
                return;
            }

            if ($utilisateur->save()) {

                $messages = new MessageManager();
                $messages->newMessage('L\'utilisateur a été sauvegardé correctement', Message::LEVEL_SUCCESS);
                $this->redirect($this->getUrlAction('listUser'));
            } else {
                $this->_newUser = $utilisateur;
                $messages = new MessageManager();
                $messages->newMessage('Un problème est survenue', Message::LEVEL_ERROR);
                $this->setTemplate('/admin/addUser.phtml');
            }
        } else {
            $this->_newUser = $utilisateur;
            $messages = new MessageManager();
            $messages->newMessage('Un des champs n\'est pas corect', Message::LEVEL_ERROR);
            $this->setTemplate('/admin/addUser.phtml');
        }
    }
    
    public function addMatchAction() {
        $post = Access::getRequest();
        $match = new MatchModel();

        if (isset($post['ronde'], $post['equipe_id_1'], $post['equipe_id_2'])) {


            if (!empty($post['ronde']) && $post['equipe_id_1'] != $post['equipe_id_2']) {
                $match->setAttribute('ronde', $post['ronde']);
                $match->setAttribute('equipe_id_1', $post['equipe_id_1']);
                $match->setAttribute('equipe_id_2', $post['equipe_id_2']);

                if ($match->save()) {

                    $messages = new MessageManager();
                    $messages->newMessage('Le match a été sauvegardé correctement', Message::LEVEL_SUCCESS);
                    $this->redirect($this->getUrlAction('listMatch'));
                } else {

                    $messages = new MessageManager();
                    $messages->newMessage('Un problème est survenue', Message::LEVEL_ERROR);
                    $this->setTemplate('/admin/addMatch.phtml');
                }
            }else {
                $messages = new MessageManager();
                $messages->newMessage('Tous les champs sont obligatoires et les équipes doivent être différentes', Message::LEVEL_ERROR);
                $this->setTemplate('/admin/addMatch.phtml');
            }

        } else {
            $messages = new MessageManager();
            $messages->newMessage('Tous les champs sont obligatoires', Message::LEVEL_ERROR);
            $this->setTemplate('/admin/addMatch.phtml');
        }
    }

    public function addPouleAction() {
        $post = Access::getRequest();
        $poule = new PouleModel();

        if (isset($post['name']) && !empty($post['name'])) {
            $poule->setAttribute('name', $post['name']);

            if ($poule->save()) {
                $messages = new MessageManager();
                $messages->newMessage('La poule a été sauvegardée correctement', Message::LEVEL_SUCCESS);
                //$this->redirect($this->getUrlAction('listPoule'));
            } else {

                $messages = new MessageManager();
                $messages->newMessage('Un problème est survenue', Message::LEVEL_ERROR);
                $this->setTemplate('/admin/addPoule.phtml');
            }

        } else {
            $messages = new MessageManager();
            $messages->newMessage('Tous les champs sont obligatoires', Message::LEVEL_ERROR);
            $this->setTemplate('/admin/addPoule.phtml');
        }
    }

    public function addEquipeAction() {
        $post = Access::getRequest();
        $equipe = new EquipeModel();

        if (isset($post['name'], $post['image']) && !empty($post['name'])) {
            $equipe->setAttribute('name', $post['name']);
            $equipe->setAttribute('image', $post['image']);

            if ($equipe->save()) {
                $messages = new MessageManager();
                $messages->newMessage('L\'équipe a été sauvegardé correctement', Message::LEVEL_SUCCESS);
            } else {

                $messages = new MessageManager();
                $messages->newMessage('Un problème est survenue', Message::LEVEL_ERROR);
                $this->setTemplate('/admin/addPoule.phtml');
            }

        } else {
            $messages = new MessageManager();
            $messages->newMessage('Tous les champs avec un * sont obligatoires', Message::LEVEL_ERROR);
            $this->setTemplate('/admin/addPoule.phtml');
        }
    }

    public function deleteUserAction() {
        $get = Access::getRequest();
        if (isset($get['id'])){
            /** @var UtilisateurModel $utilisateur */
            $utilisateur = (new UtilisateurCollection())->loadById($get['id']);

            if ($utilisateur->remove()) {
                $messages = new MessageManager();
                $messages->newMessage('Le joueur a été correctement supprimé', Message::LEVEL_SUCCESS);
            } else {
                $messages = new MessageManager();
                $messages->newMessage('Le joueur n\'a pas été correctement supprimé', Message::LEVEL_ERROR);
            }
        }

        $this->redirect($this->getUrlAction('listUser'));
        //$this->setTemplate('/admin/listUser.phtml');
    }

    public function addDateByRondeAction() {
        $post = Access::getRequest();
        $erreur = false;
        $matchs = $this->getAllMatchEnCoursByRonde($post['ronde']);
        /** @var MatchModel $match */
        foreach ($matchs as $match) {
            $match->setAttribute('date', $post['date']);
            if(!$match->save())
                $erreur = true;
        }
        if (!$erreur) {
            $messages = new MessageManager();
            $messages->newMessage('Les dates ont été correctement ajoutées', Message::LEVEL_SUCCESS);
        } else {
            $messages = new MessageManager();
            $messages->newMessage('Erreur lors de l\'ajout des dates', Message::LEVEL_ERROR);
        }
        $this->redirect($this->getUrlAction('listMatch'));
    }

    public function addScoreMatchAction() {
        $post = Access::getRequest();

        if (isset($post['match'])) {
            $matchCollection = new MatchCollection();
            foreach ($post['match'] as $matchId => $scores) {
                /** @var MatchModel $match */
                $match = $matchCollection->loadById($matchId);

                if ($match) {

                    if (trim($scores['score_equipe_1']) != "" && trim($scores['score_equipe_2']) != "") {
                        $match->setAttribute('score_equipe_1', $scores['score_equipe_1']);
                        $match->setAttribute('score_equipe_2', $scores['score_equipe_2']);
                    } else {
                        $match->setAttribute('score_equipe_1', null);
                        $match->setAttribute('score_equipe_2', null);
                    }

                    $match->save();
                }
            }

            $messages = new MessageManager();
            $messages->newMessage('Les scores ont bien été sauvegardés', Message::LEVEL_SUCCESS);
        } else {
            $messages = new MessageManager();
            $messages->newMessage('Match non trouvé', Message::LEVEL_ERROR);
        }

        $this->redirect($this->getUrlAction('listMatch'));
        //$this->setTemplate('/admin/listMatch.phtml');
    }

    /**
     * @return UtilisateurModel
     */
    public function getNewUser()
    {
        return ($this->_newUser)?$this->_newUser:new UtilisateurModel();
    }

    /**
     * @return Collection
     */
    public function getAllEquipe() {
        $_equipeCollection = new EquipeCollection();
        return $_equipeCollection->loadAll(["name" => Collection::SORT_ASC]);
    }

    /**
     * @return Collection
     */
    public function getAllPoule() {
        $_pouleCollection = new PouleCollection();
        return $_pouleCollection->loadAll(["name" => Collection::SORT_ASC]);
    }

    /**
     * @return Collection
     */
    public function getAllUser() {
        $_utilisateurCollection = new UtilisateurCollection();
        return $_utilisateurCollection->loadAll(["login" => Collection::SORT_ASC]);
    }

    /**
     * @return Collection
     */
    public function getAllMatchEnCours() {
        $_matchCollection = new MatchCollection();
        return $_matchCollection->load(["en_cours" => ["!=", 0 ]], ["ronde" => Collection::SORT_ASC]);
    }

    /**
     * @return Collection
     */
    public function getAllMatchEnCoursByRonde($ronde) {
        $_matchCollection = new MatchCollection();
        return $_matchCollection->load(["en_cours" => ["!=", 0 ], "ronde" => ["=", $ronde]], ["ronde" => Collection::SORT_ASC]);
    }

    /**
     * @return Collection
     */
    public function getMatchBegin() {
        $_matchCollection = new MatchCollection();
        return $_matchCollection->load(["date" => ["<", date('Y-m-d H:i:s')]], ["date" => Collection::SORT_DESC]);
    }

    /**
     * @return Collection
     */
    public function getMatchNotBegin() {
        $_matchCollection = new MatchCollection();
        return $_matchCollection->load(["date" => [">", date('Y-m-d H:i:s')]], ["date" => Collection::SORT_ASC]);
    }
}