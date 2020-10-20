<?php

/**
 * PHP version 7.4.9
 * 
 * @author Robin Bidanchon <robin.bidanchon@gmail.com>
 */

namespace TimePHP\Foundation;

use Twig\Environment;
use TimePHP\UrlParser\Parser;
use TimePHP\Foundation\Router;
use TimePHP\Security\CsrfToken;
use TimePHP\Foundation\Authorization;
use TimePHP\Foundation\SessionHandler;
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


    /**
     * @var Parser
     */
    protected $request;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $this->request = new Parser();
        $this->session = new SessionHandler();
        $this->authorization = new Authorization();
    }

    /**
     * Return the view with parameters
     *
     * @param string $view
     * @param array $parameters
     * @return void
     */
    public function render(string $view, array $parameters = [])
    {
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
    public function generate(string $name, array $params = [], array $flags = []): string
    {
        return Router::generate($name, $params, $flags);
    }

    /**
     * Create a session based on parameters
     *
     * @param object $user
     * @param array $params
     * @return void
     */
    public function createSession( \App\Bundle\Entity\User $user, ?array $params = []): void
    {
        if ($user === null || empty($user)) {
            throw new SessionException("createSession function requires an array, null given", 1001);
        } else if (!empty($this->session->get())) {
            throw new SessionException('A session has already been started', 1002);
        } else {
            $this->session->set("csrf_token", CsrfToken::generate())
                ->set("user", $user);
            foreach ($params as $key => $value) {
                $this->session->set($key, $value);
            }
        }
    }

    /**
     * Close the current session
     *
     * @return void
     */
    public function closeSession(): void
    {
        if (empty($this->session->get())) {
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
    public function redirectUrl(string $url): void
    {
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
    public function redirectRouteName(string $routeName, array $params = [], array $flags = []): void
    {
        if (is_string($routeName)) {
            header("Location: {$this->generate($routeName,$params,$flags)}");
        } else {
            throw new RedirectionException("$routeName doesn't exists");
        }
    }

}
