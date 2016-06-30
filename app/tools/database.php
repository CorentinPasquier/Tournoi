<?php
/**
 * Created by PhpStorm.
 * User: matpif
 * Date: 27/04/16
 * Time: 20:53
 */

include_once 'SQLite.php';
include_once 'ReadIni.php';

$SQLite = new SQLite();

$stmt = $SQLite->prepareQuery('PRAGMA user_version;');
$result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
$queries = [];

if (isset($result['user_version'])) {

    if ($result['user_version'] < 1) {

        $queries = [
            'Create Utilisateur' => 'CREATE TABLE `utilisateur` (
            `id`	INTEGER PRIMARY KEY AUTOINCREMENT,
            `login`	TEXT NOT NULL UNIQUE,
            `firstName`	TEXT,
            `lastName`	TEXT,
            `password`	TEXT NOT NULL,
            `email`	TEXT NOT NULL UNIQUE,
            `privilege`	INTEGER NOT NULL DEFAULT -1
        );',
            'Create Equipe' => 'CREATE TABLE `equipe` (
            `id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name`	TEXT,
            `image`	TEXT
        );',
            'Create Match' => 'CREATE TABLE `match` (
            `id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `ronde`	INTEGER NOT NULL,
            `en_cours`	INTEGER NOT NULL,
            `equipe_id_1`	INTEGER NOT NULL,
            `equipe_id_2`	INTEGER NOT NULL,
            `score_equipe_1`	INTEGER,
            `score_equipe_2`	INTEGER,
            `en_cours`	INTEGER,
            `date`	DATE
        );',
            'Create Simple' => 'CREATE TABLE `simple` (
            `id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `match`	INTEGER NOT NULL,
            `joueur_1`	TEXT,
            `joueur_2`	TEXT,
            `score_1_1`	INTEGER NOT NULL,
            `score_1_2`	INTEGER NOT NULL,
            `score_2_1`	INTEGER NOT NULL,
            `score_2_2`	INTEGER NOT NULL,
            `score_3_1`	INTEGER,
            `score_3_2`	INTEGER
        );',
            'Create Double' => 'CREATE TABLE `double` (
            `id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `match`	INTEGER NOT NULL,
            `joueur_1_1`  TEXT,
            `joueur_1_2`  TEXT,
            `joueur_2_1`  TEXT,
            `joueur_2_2`  TEXT,
            `score_1_1`	INTEGER NOT NULL,
            `score_1_2`	INTEGER NOT NULL,
            `score_2_1`	INTEGER NOT NULL,
            `score_2_2`	INTEGER NOT NULL,
            `score_3_1`	INTEGER,
            `score_3_2`	INTEGER
        );',
            'Create Admin user' => 'INSERT INTO utilisateur
        (login, password, email, privilege)
        values(\'admin\', \'d033e22ae348aeb5660fc2140aec35850c4da997\', \'admin@admin\', 9)',
            'Insert Equipes' =>
                'INSERT INTO equipe (name, image) 
                values(\'La Guyonniere\', \'LaGuyonniere.jpg\')
                , (\'Beaurepaire\', \'Beaurepaire.jpg\')
                , (\'Les Landes\', \'LesLandes.jpg\')
                , (\'Treize Septiers\', \'TreizeSeptiers.jpg\')
                , (\'La Verrie\', \'LaVerrie.jpg\')
                , (\'Saint Hilaire\', \'SaintHilaire.jpg\')
                , (\'Saint Malo\', \'SaintMalo.jpg\')
                , (\'La Romagne\', \'LaRomagne.jpg\')
                , (\'La Flocelliere\', \'SaintMalo.jpg\')
                , (\'Les Epesses\', \'LesEpesses.jpg\')
                , (\'Saint Andre\', \'SaintAndre.jpg\')
                , (\'Saint Mars\', \'SaintMars.jpg\')
                , (\'Chavagne\', \'Chavange.jpg\')
                , (\'Chauche\', \'Chauche.jpg\')
                ',
        ];
    }
}

$_error = false;
foreach ($queries as $key => $query) {

    echo 'Start: '.$key."\n";
    $stmt = $SQLite->prepareQuery($query);
    if (!$stmt->execute()) {
        echo 'Error: '.$key."\n";
        $_error = true;
        break;
    }

    echo 'Finish: '.$key."\n";
}

if (!$_error) {

    $_version = ReadIni::getInstance()->getAttribute('sqlite', 'user_version');
    $stmt = $SQLite->prepareQuery("PRAGMA user_version={$_version}");
    $stmt->execute();
}
