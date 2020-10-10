<?php

/**
 * PHP version 7.4.9
 * 
 * @author Robin Bidanchon <robin.bidanchon@gmail.com>
 */

namespace TimePHP\Foundation;

use Twig\Environment;
use TimePHP\Foundation\Router;
use TimePHP\Security\CsrfToken;
use TimePHP\Exception\SessionException;
use TimePHP\Exception\RedirectionException;

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

    /**
     * Create a session based on parameters
     *
     * @param array $params
     * @return void
     */
    public function createSession(array $params): void {
        if($params === null || empty($params) || count($params) === 0){
            throw new SessionException("createSession function requires an array, null given", 1001);
        } else if(count($_SESSION) > 0) {
            throw new SessionException('A session has already been started', 1002);
        } else {
            $_SESSION["csrf_token"] = CsrfToken::generate();
            foreach($params as $key => $value){
                $_SESSION[$key] = $value;
            }
        }
    }

    /**
     * Close the current session
     *
     * @return void
     */
    public function closeSession(): void {
        if(count($_SESSION) === 0){
            throw new SessionException('No session was created. $_SESSION is empty', 1003);
        } else {
            session_unset();
            session_destroy();
        }
    }

    /**
     * Redirect to a url using a specific url
     *
     * @param string $url
     * @return void
     */
    public function redirectUrl(string $url): void {
        header("Location: $url");
    }

    /**
     * Redirect to a url using a route name
     *
     * @param string $routeName
     * @param array $params
     * @param array $flags
     * @return void
     */
    public function redirectRouteName(string $routeName, array $params = [], array $flags = []): void {
        if(is_string($routeName)) {
            header("Location: {$this->generate($routeName, $params, $flags)}");
        } else {
            throw new RedirectionException("$routeName doesn't exists");
        }
    }

}