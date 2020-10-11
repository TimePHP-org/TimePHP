<?php

/**
 * PHP version 7.4.9
 * 
 * @author Robin Bidanchon <robin.bidanchon@gmail.com>
 */

namespace TimePHP\Foundation;

use AltoRouter;
use Twig\Environment;
use TimePHP\Exception\RouterException;

/**
 * @category Router
 * @package TimePHP
 * @subpackage Foundation
 */
class Router
{

    /**
     * @var AltoRouter Variable principale du router
     */
    public static $router;

    /**
     * Twig variable to inject into controller
     *
     * @var Environment
     */
    private $twig;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options, Environment $twig) {
        self::$router = new AltoRouter();
        foreach($options["types"] as $option){
            self::$router->addMatchTypes(array($option["id"] => $option["regex"]));
        }
        $this->twig = $twig;
    }

    
    /**
     * Permet de generer une url via le nom de la route
     * 
     * @param string $name Correspond au nom de la route que l'on souhaite
     * @param array|null $params (optionel) correspond au parametres à donner a l'url
     * @param array|null $flags (optionel) correspond au parametres à donner a l'url
     * @return string
     */
    public static function generate(string $name, array $params = [], array $flags = []): string {
        $url = self::$router->generate($name, $params);
        if(count($flags) === 0){
            return $url;
        } else {
            $url .= "?".http_build_query($flags);
            return $url;
        }
    }

    /**
     * Permet d'ajouter une nouvelle route
     * 
     * @param string $url Url utilisée pour lancer le controller
     * @param object $object Fonction or String representant le controller
     * @param string|null $name (optional) name of the path
     * @return self Permet de faire du fluant calling
     */
    private function get(string $url, $object, ?string $name): self {
        self::$router->map("GET", $url, $object, $name);
        return $this;
    }


    /**
     * Permet d'ajouter une nouvelle route avec la méthode post
     * 
     * @param string $url Url utilisée pour lancer le controller
     * @param object $object Fonction or String representant le controller
     * @param string|null $name (optional) name of the path
     * @return self Permet de faire du fluant calling
     */
    private function post(string $url, $object, ?string $name): self {
        self::$router->map("POST", $url, $object, $name);
        return $this;
    }

    /**
     * Appelle la bonne fonction en fonction des routes
     *
     * @return void
     */
    public function run() {
        $match = self::$router->match();

        if ($match === false) {
            if($_ENV["APP_ENV"] == 0){    
                header('HTTP/1.0 404 Not Found');
            } else {
                throw new RouterException("Undefined route : {$_SERVER['REQUEST_URI']}", 3001);
            }
        } else if(is_string($match["target"])) {
            list($controller, $function) = explode('#', $match['target']);
            if (is_callable(array(new $controller($this->twig), $function))) {
                call_user_func_array(array(new $controller($this->twig),$function), $match['params']);
            } else {
                if($_ENV["APP_ENV"] == 0){    
                    header('HTTP/1.1 500 Internal Server Error');
                } else {
                    throw new RouterException("Cannot call the function $function on $controller", 3002);
                }
            }
        } else if(is_object($match["target"]) && is_callable($match["target"])) {
            call_user_func_array($match["target"], $match["params"]);
        } else {
            if($_ENV["APP_ENV"] == 0){    
                header('HTTP/1.1 500 Internal Server Error');
            } else {
                throw new RouterException("Somethiong went wrong", 3003);
            }       
        }
    }

    /**
     * Link routes with methods
     *
     * @param array $routes
     * @return self
     */
    public function initialize(array $routes): self{
        foreach($routes as $route){
            $method = $route["method"];
            $function = (array_key_exists("controller", $route) && array_key_exists("function", $route)) ? sprintf("%s#%s", $route["controller"], $route["function"]) : $route["function"];
            $this->$method($route["url"], $function, $route["name"]);
        }
        return $this;
    }

}
