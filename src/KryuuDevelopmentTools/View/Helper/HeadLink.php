<?php
namespace KryuuDevelopmentTools\View\Helper;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Zend\View\Helper\HeadLink as HeadLinkViewHelper;
use Leafo\ScssPhp\Compiler as scssc;

class HeadLink extends HeadlinkViewHelper
{
	private $usingSass = '';
	private $publicPath = '../../../../../../public/';
	private $tmpPath = '/cache';
	private $tmpID = null;
	private $isRealPublicPath = FALSE;
	private $cacheTimeOut = 3660;
	private $caching = FALSE;
	private $isMinifying = array
	(
		'css' => FALSE,
	);
	private $time = null;
	
	
	public function __construct($publicPath='./')
	{
		if($this->publicPath == '')
		{
			$this->publicPath = $publicPath;
		}
		if($this->isRealPublicPath == FALSE)
		{
			$this->publicPath = realpath(__DIR__ . '/' . $this->publicPath);
		}
		$this->tmpID = uniqid();
		parent::__construct();
	}
	
	/**
     * Create item for stylesheet link item
     *
     * @param  array $args
     * @return stdClass|false Returns false if stylesheet is a duplicate
     */
    public function createDataStylesheet(array $args)
    {
		
		$args_tmp = $args;
        $href = array_shift($args_tmp);
		$css = '';
        $this->isCached('css');
		
		if (file_exists($this->publicPath . $href))
		{
			$file = pathinfo($href);
			$file['realpath'] = $this->publicPath . $href;
			$file['href'] = $href;
			if($file['extension'] == 'scss')
			{
				$scssFileContent = file_get_contents($file['realpath']);
				$scss = new scssc();
				$css = $scss->compile($scssFileContent);
				$file['extension'] = 'css';
				$args[0] = $this->cacheFile($css, $file, TRUE);
			} 
			elseif($file['extension'] == 'less') 
			{
				$parser = new Less_Parser();
				$parser->parseFile( $file['realpath'] );
				$css = $parser->getCss();
				$file['extension'] = 'css';
				$args[0] = $this->cacheFile($css, $file, TRUE);
			}
			
			if ($file['extension'] == 'css')
			{
				if ($this->isMinifying['css']) 
				{
					$filters = array(/*...*/);
					$plugins = array(/*...*/);

					//$minifier = new CssMinifier(file_get_contents($file), $filters, $plugins);
					//$css = $minifier->getMinified();
				}
			}
			
		}
		
		return parent::createDataStylesheet($args);
    }
	
	/**
     * Render link elements as string
     *
     * @param  string|int $indent
     * @return string
     */
	public function toString($indent = null)
    {
        $indent = (null !== $indent)
                ? $this->getWhitespace($indent)
                : $this->getIndent();

		$render = parent::toString($indent);
		
        return $this->usingSass . $render;
    }
	
	private function isCached($tmpFile, $cacheOverwrite = FALSE)
	{
		if($this->caching || $cacheOverwrite)
		{
			if (file_exists($tmpFile))
			{
				$filetime = filemtime($tmpFile);
				return $filetime+$this->cacheTimeOut > $this->getTime() ? TRUE : FALSE;
			}
			return FALSE;
		}
		return TRUE;
	}
	
	private function cacheFile($data,$file,$cacheOverwrite=FALSE)
	{
		$file['cachefilename']= $file['filename'] .'.'.$file['extension'];
		$file['cachehref']	  = $this->tmpPath . $file['dirname'] . '/' . $file['cachefilename'];
		$file['cachedirname'] = $this->publicPath . $this->tmpPath . $file['dirname'];
		$file['cachepath']	  = $file['cachedirname'] . '/' . $file['cachefilename'];

		if (!file_exists($file['cachedirname']))
		{
			mkdir($file['cachedirname'], 0777, TRUE);
		}
		
		if (!$this->isCached($file['cachepath'],$cacheOverwrite))
		{
			echo '<!-- Renewing cache for ' . $file['cachefilename'] . ' at ' . date('Y-m-d H:i:s') . "-->\n";
			file_put_contents($file['cachepath'], $data);
			return $file['cachehref'];
		}
		if($cacheOverwrite)
		{
			$datetime1 = new \DateTime();
			$datetime1->setTimestamp(filemtime($file['cachepath'])+$this->cacheTimeOut);
			$datetime2 = new \DateTime();
			$datetime2->setTimestamp($this->getTime());
			$difference = $datetime1->diff($datetime2);

			$var = '<!-- Next renewal in ' 
					.$difference->y.' year(s), ' 
					.$difference->m.' month(s), ' 
					.$difference->d.' day(s), '
					.$difference->h.' hour(s), '
					.$difference->i.' minut(s), '
					.$difference->s.' second(s) ' . "-->\n";
			var_dump($var);
			return $file['cachehref'];
		}
		return $file['href'];
	}
	
	private function getTime(){
		return $this->time != null ? $this->time : $this->time = time();
	}
}