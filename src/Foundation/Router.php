<?php

/**
 * PHP version 7.4.9
 * 
 * @author Robin Bidanchon <robin.bidanchon@gmail.com>
 */

namespace TimePHP\Foundation;

use AltoRouter;
use Whoops\Run;
use DI\Container;
use Twig\Environment;
use Whoops\Handler\PrettyPageHandler;

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
     * @var Whoops Générateur de belles pages d'erreurs
     */
    private $_whoops;

    /**
     * Twig variable to inject into controller
     *
     * @var Environment
     */
    private $twig;

    /**
     * app container
     *
     * @var Container
     */
    private $container;


    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options, Environment $twig, Container $container) {
        self::$router = new AltoRouter();
        foreach($options["types"] as $option){
            self::$router->addMatchTypes(array($option["id"] => $option["regex"]));
        }
        $this->_whoops = new Run;
        $this->_whoops->pushHandler(new PrettyPageHandler);
        $this->_whoops->register();
        $this->twig = $twig;
        $this->container = $container;
    }

    /**
     * Permet d'ajouter une nouvelle route
     * 
     * @param string $url Url utilisée pour lancer le controller
     * @param object $object Fonction or String representant le controller
     * @param string|null $name (optional) name of the path
     * @return self Permet de faire du fluant calling
     */
    public function get(string $url, $object, ?string $name): self {
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
    public function post(string $url, $object, ?string $name): self {
        self::$router->map("POST", $url, $object, $name);
        return $this;
    }

    /**
     * Permet de generer une url via le nom de la route
     * 
     * @param string $name Correspond au nom de la route que l'on souhaite
     * @param array|null $params (optionel) correspond au parametres à donner a l'url
     * @return string
     */
    public static function generate(string $name, array $params = [], array $flags = []): string {
        $url = self::$router->generate($name, $params);
        if(count($flags) === 0){
            return $url;
        } else {
            $index = 0;
            foreach($flags as $key => $value){
                $index === 0 ? $url.="?" : $url.="&";
                $url.=$key."=".$value;
                $index++;
            }
            return $url;
        }
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
            if (is_callable(array(new $controller($this->twig, $this->container), $function))) {
                call_user_func_array(array(new $controller($this->twig, $this->container),$function), $match['params']);
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
