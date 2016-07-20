<?php

/**
 * Created by PhpStorm.
 * User: corentin
 * Date: 16/07/16
 * Time: 11:05
 */
class ListJoueurController extends Controller
{

    function __construct()
    {
        parent::__construct();
        $this->_url = '/ListJoueur';
        $this->_title = 'Liste des joueurs';
        $this->_page = 'ListJoueur';
        $this->setTemplate('/listJoueur.phtml');
        $this->addJS('/js/listJoueur.js');
    }
    
    public function getListJoueurByTeam($equipe) {
        $_joueurCollection = new JoueurCollection();
        return $_joueurCollection->load(['team' => ['=', $equipe], 'visible' => ['=', '1']], ['name' => Collection::SORT_ASC]);
    }
    
    public function newPlayerAction() {
        $post = Access::getRequest();
        $erreur = false;
        if (isset($post['joueur'], $post['team'])) {
            foreach($post['joueur'] as $name) {
                if($name != "") {
                    $joueur = new JoueurModel();
                    $joueur->setAttribute('name', $name);
                    $joueur->setAttribute('team', $post['team']);
                    $joueur->setAttribute('visible', 1);
                    if (!$joueur->save())
                        $erreur = true;
                }
            }
        }
        if($erreur){
            $message = new MessageManager();
            $message->newMessage('Erreur lors de l\'enregstrement des joueurs', Message::LEVEL_ERROR);
        }
        else{
            $message = new MessageManager();
            $message->newMessage('Les joueurs ont bien été ajoutés !', Message::LEVEL_SUCCESS);
        }
        $this->setTemplate('/listJoueur.phtml');
    }

    public function deleteUserAction() {
        $get = Access::getRequest();
        if (isset($get['id'])){
            /** @var JoueurModel $joueur */
            $joueur = (new JoueurCollection())->loadById($get['id']);

            if ($joueur->hide()) {
                $messages = new MessageManager();
                $messages->newMessage('L\'utilisateur a été correctement supprimé', Message::LEVEL_SUCCESS);
            } else {
                $messages = new MessageManager();
                $messages->newMessage('L\'utilisateur n\'a pas été correctement supprimé', Message::LEVEL_ERROR);
            }
        }
        $this->setTemplate('/listJoueur.phtml');
    }
    
}