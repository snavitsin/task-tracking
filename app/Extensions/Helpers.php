<?php


namespace App\Extensions;


class Helpers
{

    /**
     * Transforms string to caselCase notation
     * @access public
     * @static
     * @param string $str
     * @param boolean $lcfirst
     * @return string
     */

    public static function toCamelCase($str, $lcfirst = true)
    {
        if(strpos($str, "_") !== false){
            $lower = strtolower($str);
            $capitalized = ucwords($lower, "_");
            $str = str_replace("_", "", $capitalized);
        }
        else{
            if(!preg_match('#[a-z]#', $str)){
                $str = strtolower($str);
            }
            $str = ucfirst($str);
        }

        $str = $lcfirst ? lcfirst($str) : $str;
        return $str;
    }
}