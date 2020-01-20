<?php

/**
 * La classe Controller permet de mettre en forme les futurs controllers
 * Elle embarque les attributs liés à la gestions de la base de données ainsi
 * qu'à l'envoie de la vue à l'utilisateur.
 * 
 * PHP version 7.4.1
 * 
 * @category Controller
 * @package TimePHP
 * @subpackage Foundation
 * @author Robin Bidanchon <robin.bidanchon@gmail.com>
 * @license MPL-2.0 https://github.com/MrAnyx/Skeleton-TimePHP/blob/master/LICENSE
 * @link any page
 */


namespace TimePHP\Foundation;

use PDO;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;
use TimePHP\Foundation\Router;

/**
 * @category Controller
 * @package TimePHP
 * @subpackage Foundation
 */
abstract class Controller
{

    /**
     * @var Environment
     */
    public $twig;


    /**
     * @var PDO
     */
    public $client;

    public function __construct()
    {
        //chargement du fichier app.ini à la racine du projet
        $lst_ini = parse_ini_file("../../app.ini");
        $this->twig = new Environment(new FilesystemLoader(__DIR__ . "/../../". $lst_ini["view_path"]));

        // ajout de la fonction asset pour twig afin de récuperer l'url du dossier asset dans le repertoire public
        $this->twig->addFunction(new TwigFunction('asset', function ($asset)
        {
            return sprintf('/../assets/%s', ltrim($asset, '/'));
        }));
        $this->twig->addFunction(new TwigFunction('generate', function (string $name, array $params)
        {
            return sprintf(Router::$router->generate($name, $params));
        }));
        $this->twig->addFunction(new TwigFunction('dump', function ($object)
        {
            return var_dump($object);
        }));

        $str_connexion = "mysql:host=".$lst_ini["my_host"].";dbname=".$lst_ini["my_name"];
        $this->client = new PDO($str_connexion, $lst_ini["my_user"], $lst_ini["my_pass"]);
        // permet d'afficher un rapport des erreurs
        $this->client->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // indique que le mode par défaut pour le fetch est FETCH_ASSOC
        $this->client->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
}