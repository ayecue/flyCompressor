<?php
	/**
	 * Compressor - A static JS/CSS compressor
	 * 
	 * @package		StaticCompressor
	 * @author		swe <soerenwehmeier@googlemail.com>
	 * @version		0.1.0.0
	 *
	 * Todo:
	 * - add some functions
	 *
	 */
	class StaticCompressor {
		static private $cssLoader = NULL;
		static private $jsLoader = NULL;
		
		public static function loadCSSFile($array, $filename = ''){
			self::getCSSLoader()->loadCSSFile($array, $filename);
		}
		
		public static function getCSSScript($cache = NULL){
			return self::getCSSLoader()->getCSSScript($cache);
		}
		
		public static function loadJSFile($array, $filename = ''){
			self::getJSLoader()->loadJSFile($array, $filename);
		}
		
		public static function getJSScript($cache = NULL){
			return self::getJSLoader()->getJSScript($cache);
		}
		
		public static function createCSSLoader(){
			isset(self::$cssLoader) && self::$cssLoader->destroy();
			
			self::$cssLoader = new CSSCompressor();
		}
		private static function getCSSLoader(){return !empty(self::$cssLoader) ? self::$cssLoader : (self::$cssLoader = new CSSCompressor());}
		
		public static function createJSLoader(){
			isset(self::$jsLoader) && self::$jsLoader->destroy();
			
			self::$jsLoader = new JSCompressor();
		}
		private static function getJSLoader(){return !empty(self::$jsLoader) ? self::$jsLoader : (self::$jsLoader = new JSCompressor());}
		
		public function setDirectory($directory){
			self::getCSSLoader()->setDirectory($directory);
			self::getJSLoader()->setDirectory($directory);
		}
		
		public function setHTTP($http){
			self::getCSSLoader()->setHTTP($http);
			self::getJSLoader()->setHTTP($http);
		}
		
		public function enableDebug($mode = true){
			self::getCSSLoader()->enableDebug($mode);
			self::getJSLoader()->enableDebug($mode);
		}
	}
?>