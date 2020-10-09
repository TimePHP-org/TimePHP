<?php

/**
 * PHP version 7.4.9
 * 
 * @author Robin Bidanchon <robin.bidanchon@gmail.com>
 */

namespace TimePHP\Foundation;

use AltoRouter;
use Twig\Environment;

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
            header('HTTP/1.0 404 Not Found');
        } else if(is_string($match["target"])) {
            list($controller, $function) = explode('#', $match['target']);
            if (is_callable(array(new $controller($this->twig), $function))) {
                call_user_func_array(array(new $controller($this->twig),$function), $match['params']);
            } else {
                header('HTTP/1.1 500 Internal Server Error');
            }
        } else if(is_object($match["target"]) && is_callable($match["target"])) {
            call_user_func_array($match["target"], $match["params"]);
        } else {
            header('HTTP/1.1 500 Internal Server Error');
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
            $function = (array_key_exists("controller", $route)) ? sprintf("%s#%s", $route["controller"], $route["function"]) : $route["function"];
            $this->$method($route["url"], $function, $route["name"]);
        }
        return $this;
    }

}
