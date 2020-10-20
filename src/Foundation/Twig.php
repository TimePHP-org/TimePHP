<?php

namespace TimePHP\Foundation;

use Closure;
use Twig\TwigFilter;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;
use TimePHP\Exception\TwigException;
use TimePHP\Foundation\Twig\FilterTwig;
use TimePHP\Foundation\Twig\FunctionTwig;

class Twig
{

   /**
    * Twig variable
    *
    * @var Environment
    */
   private $twig;

   /**
    * array of custom options
    *
    * @param array $options
    */
   public function __construct(array $options)
   {

      $this->twig = new Environment(new FilesystemLoader(__DIR__ . "/../../../../../App/Bundle/Views"));

      $function = new FunctionTwig();
      $filter = new FilterTwig();

      $this->twig->addFunction(new TwigFunction('asset', Closure::fromCallable([$function, "asset"])));
      $this->twig->addFunction(new TwigFunction('component', Closure::fromCallable([$function, "component"])));
      $this->twig->addFunction(new TwigFunction('generate', Closure::fromCallable([$function, "generate"])));
      $this->twig->addFunction(new TwigFunction('provideCsrf', Closure::fromCallable([$function, "provideCsrf"]),['is_safe' => ['html']]));
      $this->twig->addFunction(new TwigFunction('dump', Closure::fromCallable([$function, "dump"])));
      $this->twig->addFunction(new TwigFunction("get", Closure::fromCallable([$function, "get"])));
      $this->twig->addFunction(new TwigFunction("isConnected", Closure::fromCallable([$function, "isConnected"])));
      $this->twig->addFunction(new TwigFunction("isAdmin", Closure::fromCallable([$function, "isAdmin"])));
      $this->twig->addFunction(new TwigFunction("isUser", Closure::fromCallable([$function, "isUser"])));

      $this->twig->addFilter(new TwigFilter("truncate", Closure::fromCallable([$filter, "truncate"])));
      $this->twig->addFilter(new TwigFilter("formatDate", Closure::fromCallable([$filter, "formatDate"])));

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
