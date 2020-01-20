<?php

/**
 * Le fichier functions.php sert à créer des fonctions.
 * Ces fonctions pourront êre utilisées dans les fichier
 * du framework via l'autoload file de composer
 * 
 * PHP version 7.4.1
 * 
 * @category Functions
 * @package None
 * @subpackage None
 * @author Robin Bidanchon <robin.bidanchon@gmail.com>
 * @license MPL-2.0 https://github.com/MrAnyx/Skeleton-TimePHP/blob/master/LICENSE
 * @link any page
 */


use Cocur\Slugify\Slugify;

/**
 * Convert the title into a slug
 * @param string $title     Titre que l'on veut convertir en slug
 * @param string $separator     Separateur que l'on va utiliser pour la conversion en slug
 * @return array
 */
function slugify(string $title, string $separator): string
{
    $slug = new Slugify();
    return $slug->slugify($title, $separator);
}

/**
 * Convert strings that represent integers into real integers
 * @param array $array correspond à la variable $match qui est placée en paramètre
 * @return array
 */
function Convert_Array_Element_To_Int(array $array): array
{
    // conversion des nombres de l'url en entier car de base, se sont des strings
    foreach($array["params"] as $key => $value) {
        if(is_numeric($value) && gettype($value) === "string") {
            $array["params"][$key] = intval($value);
        }
    }
    return $array;
}