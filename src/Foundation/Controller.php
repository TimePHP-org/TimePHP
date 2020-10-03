<?php

/**
 * PHP version 7.4.9
 * 
 * @author Robin Bidanchon <robin.bidanchon@gmail.com>
 */

namespace TimePHP\Foundation;

use DI\Container;
use Twig\Environment;
use TimePHP\Foundation\Router;

/**
 * @category Controller
 * @package TimePHP
 * @subpackage Foundation
 */
abstract class Controller
{

    /**
     * @var Environment Permet de retourner la vue correspondante à l'utilisateur
     */
    protected $twig;

    /**
     * App container
     *
     * @var Container
     */
    protected $container;

    public function __construct(Environment $twig, Container $container){

        $this->twig = $twig;
        $this->container = $container;

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

}