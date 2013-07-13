<?php

/** 
 * 13/07/2013 The bases of design Quest Framework v 0.1
 * @author: Absalón Hernández
 * @website: http://absa.me
 * @email: me@absa.me
 * @description: Core of Quest Framework
 */
 
class Quest{



}


/**
 * The name of the current controller.
 * @var string
 */
public static $controllerName = "";


/**
 * An instnace of the current controller object.
 * @var ETController
 */
public static $controller;


/**
 * An instance of the ETSession class.
 * @var ETSession
 */
public static $session;


/**
 * An instance of the ETDatabase class.
 * @var ETDatabase
 */
public static $database;


/**
 * An instance of the ETCache class.
 * @var ETCache
 */
public static $cache;


/**
 * Shortcut function to fetch a new ETSQLQuery object (or an ETSQLResult object if a query string is passed.)
 *
 * @param string $sql An optional SQL query string to run.
 * @return ETSQLQuery|ETSQLResult
 */
public static function SQL($sql = "")
{
	if ($sql) return ET::$database->query($sql);
	else return ETFactory::make("sqlQuery");
}


/**
 * Trigger an event and call event handlers within plugins.
 *
 * @param string $event The name of the event.
 * @param array $parameters An array of parameters to pass to the event handlers as arguments.
 * @return array An array of values returned by the event handlers.
 */
public static function trigger($event, $parameters = array())
{
	$returns = array();
	foreach (self::$plugins as $plugin) {
		if (method_exists($plugin, "handler_$event")) {
			$return = call_user_func_array(array($plugin, "handler_$event"), $parameters);
			if ($return !== null) $returns[] = $return;
		}
	}
	return $returns;
}

/**
 * An array of configuration settings.
 * @var array
 */
public static $config = array();


/**
 * Load values from a config file into the config array.
 *
 * @param string $file The config file to load values from.
 * @return void
 */
public static function loadConfig($file)
{
	include $file;
	ET::$config = array_merge(ET::$config, $config);
}


/**
 * Fetch the value of a configuration option, falling back to a default if it isn't set.
 *
 * @param string $key The name of the configuration option.
 * @param mixed $default A default value to fall back on if the config option isn't set.
 * @return mixed The value of the config option.
 */
public static function config($key, $default = null)
{
	return isset(ET::$config[$key]) ? ET::$config[$key] : $default;
}




/**
 * Halt page execution and show a fatal error message.
 *
 * @param Exception $exception The exception that was the cause of the fatal error.
 * @return void
 */
public static function fatalError($exception)
{
	// Get the information about the exception.
	$errorNumber = $exception->getCode();
	$message = $exception->getMessage();
	$file = $exception->getFile();
	$line = $exception->getLine();
	$backtrace = $exception->getTrace();

	// Use the controller's response type, or just use the default one.
	$responseType = (self::$controller and self::$controller->responseType) ? self::$controller->responseType : RESPONSE_TYPE_DEFAULT;

	// Clean the output buffer and send headers if possible.
	@ob_end_clean();
	if (!headers_sent()) {
		header("HTTP/1.0 500 Internal Server Error");
		header("Content-Type: text/html; charset=utf-8");
	}

	// See if we can get the lines of the file that caused the error.
	if (is_string($file) and is_numeric($line) and file_exists($file)) $errorLines = file($file);
	else $errorLines = false;

	$data = array();
	$data["pageTitle"] = T("Fatal Error");

	// Render the view into $data["content"], so it will be outputted within the master view.
	ob_start();
	include PATH_VIEWS."/error.php";
	$data["content"] = ob_get_contents();
	ob_end_clean();

	// Render the master view, or just output the content if we can't find one.
	if ($responseType === RESPONSE_TYPE_DEFAULT and file_exists($view = PATH_VIEWS."/message.master.php"))
		include $view;
	else
		echo $data["content"];

	exit;
}


/**
 * Render a "404 Not Found" error.
 *
 * @return void
 */
public static function notFound()
{
	header("HTTP/1.1 404 Not Found");

	$data = array();
	$data["pageTitle"] = T("Page Not Found");

	// Render the view into $data["content"], so it will be outputted within the master view.
	ob_start();
	include PATH_VIEWS."/404.php";
	$data["content"] = ob_get_contents();
	ob_end_clean();

	// Render the master view.
	include PATH_VIEWS."/message.master.php";
	exit;
}

}