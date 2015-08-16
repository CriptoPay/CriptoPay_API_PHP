<?php
/**
 * Copyright (c) 2014-2015 CriptoPay
 */

namespace CriptoPayApiRest\src\Comun;
use CriptoPayApiRest\src\Excepciones;

/**
 * 
 * Autocargador ApiRest CriptoPay
 * 
 * @package CriptoPayApiRest
 * @version 2.2
 */

class AutoLoader
{
    /**
     * @var string The namespace prefix for this instance.
     */
    protected $namespace = '';

    /**
     * @var string The filesystem prefix to use for this instance
     */
    protected $path = '';

    /**
     * Build the instance of the autoloader
     *
     * @param string $namespace The prefixed namespace this instance will load
     * @param string $path      The filesystem path to the root of the namespace
     */
    public function __construct($namespace, $path)
    {
        $this->namespace = ltrim($namespace, '\\');
        $this->path      = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
    }

    /**
     * Try to load a class
     *
     * @param string $class The class name to load
     *
     * @return boolean If the loading was successful
     */
    public function load($class)
    {
        $class = ltrim($class, '\\');

        if (strpos($class, $this->namespace) === 0) {
            $nsparts   = explode('\\', $class);
            $class     = array_pop($nsparts);
            $nsparts[] = '';
            unset($nsparts[0]);
            $path      = $this->path . implode(DIRECTORY_SEPARATOR, $nsparts);
            $path     .= str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';

            if (file_exists($path)) {
                require $path;
                return true;
            }else{
                debug_print_backtrace();
                var_dump($path);
                //throw new Excepciones\Excepcion("No existe el fichero para autocarga->".$path);
                exit;
            }
        }

        return false;
    }

    /**
     * Register the autoloader to PHP
     *
     * @return boolean The status of the registration
     */
    public function register()
    {
        return spl_autoload_register(array($this, 'load'));
    }

    /**
     * Unregister the autoloader to PHP
     *
     * @return boolean The status of the unregistration
     */
    public function unregister()
    {
        return spl_autoload_unregister(array($this, 'load'));
    }
}