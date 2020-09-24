<?php

/**
 * PHP version 7.4.9
 * 
 * @author Robin Bidanchon <robin.bidanchon@gmail.com>
 */

namespace TimePHP\Foundation;

use Twig\Environment;
use Twig\TwigFunction;
use TimePHP\Foundation\Router;
use Twig\Loader\FilesystemLoader;
use Illuminate\Container\Container;
use Symfony\Component\Dotenv\Dotenv;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;



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

    public function __construct()
    {
        //chargement du fichier app.ini à la racine du projet
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../config/.env');

        $this->twig = new Environment(new FilesystemLoader(__DIR__ . "/../../". $_ENV['VIEW_PATH']));

        // ajout de la fonction asset pour twig afin de récuperer l'url du dossier asset dans le repertoire public
        $this->twig->addFunction(new TwigFunction('asset', function ($asset): string
        {
            return sprintf('/assets/%s', ltrim($asset, '/'));
        }));
        $this->twig->addFunction(new TwigFunction('component', function ($component): string
        {
            return sprintf('components/%s', ltrim($component, '/'));
        }));
        $this->twig->addFunction(new TwigFunction('generate', function (string $nameUrl, array $params = [], array $flags = []): string
        {
            return sprintf(Router::generate($nameUrl, $params, $flags));
        }));

        $this->twig->addFunction(new TwigFunction('dump', function ($object): string
        {
            ob_start();
            dump($object);
            return ob_get_clean();
        }));

        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $_ENV['DB_HOST'],
            'database'  => $_ENV['DB_NAME'],
            'username'  => $_ENV['DB_USER'],
            'password'  => $_ENV['DB_PASS'],
            'port'      => $_ENV['DB_PORT'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);
        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

    }

}