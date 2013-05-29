<?php
	/**
	 * Compressor - A JS compressor
	 * 
	 * @package		JSCompressor
	 * @author		swe <soerenwehmeier@googlemail.com>
	 * @version		0.1.0.0
	 *
	 * Todo:
	 * - add some functions
	 *
	 */
	class JSCompressor extends Compressor {
		protected function getPatternExtension(){
			return '/(?:\.(js))$/i';
		}
	
		protected function minify($output){
			return JSMin::minify($output);
		}
		
		public function loadJSFile($array, $filename = '') {
			self::load($array,$filename,'js');
		}
		
		public function getJSScript($cache = NULL){
			return self::parse($cache);
		}
		
		protected function parseDebug($file,$cache = NULL){
			return '<script type="text/javascript" src="'.self::getURLInfo($file).(!empty($cache) ? '?'.$cache : '').'"></script>';
		}
		
		protected function parsePlain($cache = NULL){
			return '<script type="text/javascript">'.self::getLastOutput().(!empty($cache) ? '?'.$cache : '').'</script>';
		}
		
		protected function parseSuccess($cache = NULL){
			return '<script type="text/javascript" src="'.self::getURLInfo(self::getLastName()).(!empty($cache) ? '?'.$cache : '').'"></script>';
		}
	}
?>