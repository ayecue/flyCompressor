<?php
	/**
	 * Compressor - A CSS compressor
	 * 
	 * @package		CSSCompressor
	 * @author		swe <soerenwehmeier@googlemail.com>
	 * @version		0.1.0.0
	 *
	 * Todo:
	 * - add some functions
	 *
	 */
	class CSSCompressor extends Compressor {
		protected function getPatternExtension(){
			return '/(?:\.(css))$/i';
		}
	
		protected function minify($output){
			return CssMin::minify($output);
		}
		
		public function loadCSSFile($array, $filename = '') {
			self::load($array,$filename,'css');
		}
		
		public function getCSSScript($cache = NULL){
			return self::parse($cache);
		}
		
		protected function parseDebug($file,$cache = NULL){
			return '<link rel="stylesheet" type="text/css" href="'.self::getURLInfo($file).(!empty($cache) ? '?'.$cache : '').'" />';
		}
		
		protected function parsePlain($cache = NULL){
			return '<style type="text/css">'.self::getLastOutput().(!empty($cache) ? '?'.$cache : '').'</style>';
		}
		
		protected function parseSuccess($cache = NULL){
			return '<link rel="stylesheet" type="text/css" href="'.self::getURLInfo(self::getLastName()).(!empty($cache) ? '?'.$cache : '').'" />';
		}
	}
?>