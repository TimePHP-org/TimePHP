<?php

/**
 * PHP version 7.4.9
 * 
 * @author Robin Bidanchon <robin.bidanchon@gmail.com>
 */

namespace TimePHP\Foundation;

use Twig\Environment;
use TimePHP\Foundation\Router;

/**
 * @category Controller
 * @package TimePHP
 * @subpackage Foundation
 */
abstract class AbstractController
{

    /**
     * @var Environment Permet de retourner la vue correspondante à l'utilisateur
     */
    protected $twig;

    public function __construct(Environment $twig){
        $this->twig = $twig;
    }

    /**
     * Return the view with parameters
     *
     * @param string $view
     * @param array $parameters
     * @return void
     */
    public function render(string $view, array $parameters = []){
        echo $this->twig->render($view, $parameters);
    }

    /**
     * Permet de generer une url via le nom de la route
     * 
     * @param string $name Correspond au nom de la route que l'on souhaite
     * @param array|null $params (optionel) correspond au parametres à donner a l'url
     * @param array|null $flags (optionel) correspond au parametres à donner a l'url
     * @return string
     */
    public function generate(string $name, array $params = [], array $flags = []): string {
        return Router::generate($name, $params, $flags);
    }

}