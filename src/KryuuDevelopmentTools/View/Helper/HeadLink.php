<?php
namespace KryuuDevelopmentTools\View\Helper;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Zend\View\Helper\HeadLink as HeadLinkViewHelper;
use Leafo\ScssPhp\Compiler as scssc;

class HeadLink extends HeadLinkViewHelper
{
	private $usingSass          = null;
	private $publicPath         = null;
	private $tmpPath            = null;
	private $tmpID              = null;
	private $isRealPublicPath   = null;
	private $cacheTimeOut       = null;
	private $caching            = null;
	private $minifying          = null;
	private $time               = null;
	
	
	public function __construct()
	{
		parent::__construct();
	}
    
    public function setConfig($config)
    {
        $this->usingSass        = isset($config['using sass']) 
                                    && $this->usingSass == null ? $config['using sass'] : null;
        $this->publicPath       = isset($config['public path']) 
                                    && $this->publicPath == null ? $config['public path'] : null;
        $this->tmpPath          = isset($config['tmp path']) 
                                    && $this->tmpPath == null ? $config['tmp path'] : null;
        $this->tmpID            = isset($config['tmp id']) 
                                    && $this->tmpID == null ? $config['tmp id'] : null;
        $this->isRealPublicPath = isset($config['is real public path']) 
                                    && $this->isRealPublicPath == null ? $config['is real public path'] : null;
        $this->cacheTimeOut     = isset($config['cache timeout']) 
                                    && $this->cacheTimeOut == null ? $config['cache timeout'] : null;
        $this->caching          = isset($config['caching']) 
                                    && $this->caching == null ? $config['caching'] : null;
        $this->minifying        = isset($config['minifying']) 
                                    && $this->minifying == null ? $config['minifying'] : null;

		if($this->isRealPublicPath == FALSE)
		{
			$this->publicPath = realpath(__DIR__ . '/' . $this->publicPath);
		}
		//$this->tmpID = uniqid();
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
				if ($this->minifying['css']) 
				{
					$filters = array(/*...*/);
					$plugins = array(/*...*/);

					//$minifier = new CssMinifier(file_get_contents($file), $filters, $plugins);
					//$css = $minifier->getMinified();
                    $css = file_get_contents($file['realpath']);
                    $args[0] = $this->cacheFile($css, $file, TRUE);
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
            $date = new \DateTime();
			echo '<!-- Renewing cache for ' . $file['cachefilename'] . ' at ' . $date->format('Y-m-d H:i:s') . "-->\n";
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
            
			return $file['cachehref'];
		}
		return $file['href'];
	}
	
	private function getTime(){
		return $this->time != null ? $this->time : $this->time = time();
	}
}