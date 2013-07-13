<?php

/**
 * Index.php of Quest Framework
 * @author Absalón Hernández 
 * @version 0.1
 */
 
/**
 * Denegando acceso directo a los scripts
 */
define('IN_QUEST', TRUE);

/**
 * Tiempo de Inicio
 */
define('PAGE_START_TIME', microtime(true));

/**
 * Directorios
 */

define('QUEST_ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
define('QUEST_APP', QUEST_ROOT.DS.'app');

require QUEST_APP.DS.'webroot'.DS.'bootstrap.php';
