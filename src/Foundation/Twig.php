<?php

namespace TimePHP\Foundation;

use Closure;
use DateTime;
use Illuminate\Support\Facades\Session;
use Twig\Markup;
use Twig\TwigFilter;
use Twig\Environment;
use Twig\TwigFunction;
use TimePHP\UrlParser\Parser;
use Twig\Loader\FilesystemLoader;
use TimePHP\Exception\TwigException;
use TimePHP\Foundation\SessionHandler;

class Twig
{

   /**
    * Twig variable
    *
    * @var Environment
    */
   private $twig;


   /**
    * session handler
    *
    * @var SessionHandler
    */
   private $session;


   /**
    * Url parser
    *
    * @var Parser
    */
   private $request;

   /**
    * array of custom options
    *
    * @param array $options
    */
   public function __construct(array $options)
   {

      $this->session = new SessionHandler();
      $this->request = new Parser();

      $this->twig = new Environment(new FilesystemLoader(__DIR__ . "/../../../../../App/Bundle/Views"));

      $this->twig->addFunction(new TwigFunction('asset', function ($asset): string {
         return sprintf('/assets/%s', ltrim($asset, '/'));
      }));
      $this->twig->addFunction(new TwigFunction('component', function ($component): string {
         return sprintf('components/%s', ltrim($component, '/'));
      }));
      $this->twig->addFunction(new TwigFunction('generate', function (string $nameUrl, array $params = [], array $flags = []): string {
         return sprintf(Router::generate($nameUrl, $params, $flags));
      }));
      $this->twig->addFunction(new TwigFunction('provideCsrf', function (string $csrfInputName = "csrf_token"): string {
         return !empty($_SESSION) ? new Markup("<input type=\"hidden\" name=\"$csrfInputName\" value=\"{$_SESSION["csrf_token"]}\"/>", "utf-8") : "";
      }, ['is_safe' => ['html']]));
      $this->twig->addFunction(new TwigFunction('dump', function ($object): string {
         ob_start();
         dump($object);
         return ob_get_clean();
      }));
      $this->twig->addFunction(new TwigFunction("get", function (string $param) {
         return $this->request->get($param) !== null ? $this->request->get($param) : null;
      }));
      $this->twig->addFunction(new TwigFunction("isConnected", function () {
         return $this->session->get("csrf_token") !== null;
      }));
      $this->twig->addFunction(new TwigFunction("isAdmin", function () {
         return $this->session->get("csrf_token") !== null && $this->session->get("user")->role === "admin";
      }));
      $this->twig->addFunction(new TwigFunction("isUser", function () {
         return $this->session->get("csrf_token") !== null && $this->session->get("user")->role === "user";
      }));



      $this->twig->addFilter(new TwigFilter("truncate", function (string $text, int $length) {
         return substr($text, 0, $length);
      }));
      $this->twig->addFilter(new TwigFilter("formatDate", function (DateTime $date, string $format) {
         $date = new DateTime($date);
         return $date->format($format);
      }));


      foreach ($options["twig"] as $function) {

         $name = $function["name"];

         if (array_key_exists("function", $function) && is_object($function["function"])) {
            if ($function["type"] === "function") {
               $this->twig->addFunction(new TwigFunction($name, $function["function"]));
            } elseif ($function["type"] === "filter") {
               $this->twig->addFilter(new TwigFilter($name, $function["function"]));
            }
         } else if (array_key_exists("class", $function) && array_key_exists("function", $function) && is_callable([new $function["class"], $function["function"]])) {
            $callable = Closure::fromCallable([new $function["class"], $function["function"]]);
            if ($function["type"] === "function") {
               $this->twig->addFunction(new TwigFunction($name, $callable));
            } elseif ($function["type"] === "filter") {
               $this->twig->addFilter(new TwigFilter($name, $callable));
            }
         } else {
            if ($_ENV["APP_ENV"] == 0) {
               header('HTTP/1.1 500 Internal Server Error');
            } else {
               if ($function["type"] === "function") {
                  throw new TwigException("Cannot add the custom twig function : $name", 4001);
               } elseif ($function["type"] === "filter") {
                  throw new TwigException("Cannot add the custom twig filter : $name", 4002);
               } else {
                  throw new TwigException("{$function["type"]} is not a valid twig option type. Must be either function or filter.", 4003);
               }
            }
         }
      }
   }

   /**
    * Return Twig variable
    *
    * @return Environment
    */
   public function getRenderer(): Environment
   {
      return $this->twig;
   }
}
