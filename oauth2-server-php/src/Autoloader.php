<?php

/**
 * Autoloads OAuth2 classes
 *
 * @author    Brent Shaffer <bshafs at gmail dot com>
 * @license   MIT License
 */
class Autoloader
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @param string $dir
     */
    public function __construct($dir = null)
    {
        if (is_null($dir)) {
            $dir = dirname(__FILE__).'/';
        }
        $this->dir = $dir;
    }

    /**
     * Registers OAuth2\Autoloader as an SPL autoloader.
     */
    public static function register($dir = null)
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self($dir), 'autoload'));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class - A class name.
     * @return boolean      - Returns true if the class has been loaded
     */
    public function autoload($class)
    {
        //print("Check starts!");
        if ( (0 !== strpos($class, 'Elliptic')) && 
             (0 !== strpos($class, 'BN')) && 
             (0 !== strpos($class, 'BI')) &&
             (0 !== strpos($class, 'OAuth2')) ) 
        {
            return;
        }

        if(0 === strpos($class, 'Elliptic'))
        {
            $pathToFile = str_replace("Elliptic","", $class);
            $pathToFile = ($this->dir.'lib'.str_replace('\\', '/', $pathToFile).'.php');
            //$pathToFile = str_replace("../","", $class);
        }
        else if(0 === strpos($class, 'BN'))
        {
            $pathToFile = str_replace("BN","", $class);
            $pathToFile = ($this->dir.'vendor/simplito/bn-php/lib'.str_replace('\\', '/', $pathToFile).'.php');
            $pathToFile = str_replace("/.php","/BN.php", $pathToFile); // for BN.php file
        }
        else if(0 === strpos($class, 'BI'))
        {
            $pathToFile = str_replace("BI","", $class);
            $pathToFile = ($this->dir.'vendor/simplito/bigint-wrapper-php/lib'.str_replace('\\', '/', $pathToFile).'.php');
        }
        else if(0 === strpos($class, 'OAuth2'))
        {
            $pathToFile = ($this->dir.str_replace('\\', '/', $class).'.php');
        }
        // print "<br>".$pathToFile."<br>";

        if (file_exists($file = $pathToFile)) {
            require $file;
        }
    }
}
