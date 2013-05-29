<?php
	/**
	 * Compressor - A JS/CSS compressor
	 * 
	 * @package		Compressor
	 * @author		swe <soerenwehmeier@googlemail.com>
	 * @version		0.1.0.0
	 *
	 * Todo:
	 * - add some functions
	 *
	 */
	abstract class Compressor {
		const MODE_DEBUG		= 	0;
		const MODE_PLAIN		= 	1;
		const MODE_SUCCESS		= 	2;
		const FILE_EXTENSION 	=	'min';
		const PATTERN_FOLDER	= 	'/((?:^|\/)([^\/]+))/';
		
		private $lastFile	=	NULL;
		private $lastFiles	=	NULL;
		private $lastExt	=	NULL;
		private $lastName	=	NULL;
		private $lastHash	=	NULL;
		private $lastMode	=	NULL;
		private $lastOutput	=	NULL;
		private $debug		=	false;
		
		private $http 		= 	NULL;
		private $dir 		= 	NULL;
		
		public function __construct($directory = '',$http = ''){
			self::setDirectory($directory);
			self::setHTTP($http);
		}
		
		protected function load($array, $filename, $ext){
			if (!empty($array))
			{
				self::setLastExtension($ext);
				self::clearLastFiles();
				self::setLastHash(implode('.',$array));
				self::setLastName((!empty($filename) ? $filename : self::getLastHash()));
				self::setLastFile('/'.self::getDirectory().'/'.self::getLastName());
				
				$lastUpdate = 0;
				
				foreach($array as $file)
				{
					$path = '/'.self::getDirectory().'/'.self::getPathInfo($file);
					
					if (file_exists($path))
					{
						self::pushLastFiles($file);
						$time = filemtime($path);
						$lastUpdate < $time && ($lastUpdate = $time);
					}
				}
				
				if (self::isDebug())
				{
					return self::setMode(self::MODE_DEBUG);
				}
				
				if (!file_exists(self::getLastFile()) || filemtime(self::getLastFile()) < $lastUpdate)
				{
					return self::create();
				}
				
				self::setMode(self::MODE_SUCCESS);
			}
		}
		
		private function create (){
			if (!empty($this->lastName) && !empty($this->lastFiles))
			{
				$buffer = array();
				
				foreach (self::getLastFiles() as $file)
				{
					$buffer[] = file_get_contents('/'.self::getDirectory().'/'.self::getPathInfo($file),'r'); 
				}
				
				$output = $this->minify(implode("\n",$buffer));
				
				if (empty($output))
				{
					return self::setMode(self::MODE_DEBUG);
				}
				
				if(!@file_put_contents('/'.self::getDirectory().'/'.self::getLastName(), $output))
				{
					self::setLastOutput($output);
					return self::setMode(self::MODE_PLAIN);
				}
				
				self::setMode(self::MODE_SUCCESS);
			}
		}
		
		abstract protected function minify($output);
		
		protected function parse($cache = NULL){
			switch(self::getMode())
			{
				case self::MODE_DEBUG:
					$lastFiles = self::getLastFiles();
					
					if (!empty($lastFiles))
					{
						$output = '';
					
						foreach($lastFiles as $file)
						{
							$output .= $this->parseDebug($file,$cache);
						}
						
						return $output;
					}
				
					break;
				case self::MODE_PLAIN:
					return $this->parsePlain($cache);
				
					break;
				case self::MODE_SUCCESS:
					return $this->parseSuccess($cache);
				
					break;
			}
			
			return NULL;
		}
		
		abstract protected function parseDebug($file,$cache = NULL);
		abstract protected function parsePlain($cache = NULL);
		abstract protected function parseSuccess($cache = NULL);
		
		protected function getDirectoryInfo($directory){
			preg_match_all(self::PATTERN_FOLDER,$directory,$matches,PREG_SET_ORDER);
			$buffer = array();
			
			foreach ($matches as $match){
				$buffer[] = array_pop($match);
			}
			
			return $buffer;
		}
		
		protected function getFullInfo($file){
			$directory 	= self::getDirectoryInfo($file);
			$filename 	= array_pop($directory);
			
			return array(
				'directory' 	=> 	implode('/',$directory),
				'filename' 		=> 	preg_replace($this->getPatternExtension(),'',$filename),
				'extension' 	=> 	preg_match($this->getPatternExtension(),$filename,$matches) ? $matches[1] : self::getLastExtension()
			);
		}
		
		protected function getPathInfo($file){
			$info = self::getFullInfo($file);
			
			return (!empty($info['directory']) ? $info['directory'].'/' : '').$info['filename'].'.'.$info['extension'];
		}
		
		protected function getURLInfo($file){
			$http = self::getHTTP();
			
			return (!empty($http) ? $http : '').self::getPathInfo($file);
		}
		
		private function setLastFile($file){$this->lastFile = $file;}
		protected function getLastFile(){return $this->lastFile;}
		
		private function setLastFiles($files){$this->lastFiles = $files;}
		private function pushLastFiles($file){$this->lastFiles[] = $file;}
		private function clearLastFiles(){self::setLastFiles(array());}
		protected function getLastFiles(){return $this->lastFiles;}
		
		private function setLastExtension($ext){$this->lastExt = $ext;}
		protected function getLastExtension(){return $this->lastExt;}
		
		private function setLastOutput($output){$this->lastOutput = $output;}
		protected function getLastOutput(){return $this->lastOutput;}
		
		private function setLastName($name){$this->lastName = $name.'.'.implode('.',array(self::FILE_EXTENSION,self::getLastExtension()));}
		protected function getLastName(){return $this->lastName;}
		
		private function setLastHash($string){$this->lastHash = md5($string);}
		protected function getLastHash(){return $this->lastHash;}
		
		public function setDirectory($directory){
			$buffer = self::getDirectoryInfo($directory);

			!empty($buffer) && ($this->dir = implode('/',$buffer));
		}
		protected function getDirectory(){return $this->dir;}
		
		public function setHTTP($http){$this->http = $http;}
		protected function getHTTP(){return $this->http;}
		
		private function setMode($mode){$this->mode = $mode;}
		protected function getMode(){return $this->mode;}
		
		public function enableDebug($mode = true){$this->debug = $mode;}
		protected function isDebug(){return $this->debug;}
		
		abstract protected function getPatternExtension();
	}
?>