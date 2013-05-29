<?php
	define('ROOT',__DIR__.'/');
	define('HTTP','http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/');
	
	function __autoload($strClassName)
	{
		$strLibrary = ROOT.'src/'.$strClassName.'.php';

		if (file_exists($strLibrary))
		{
			require_once($strLibrary);
			return;
		}
	}
?>