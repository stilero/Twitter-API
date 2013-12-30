<?php
/**
 * Class for handling FB Responses
 *
 * @version  1.0
 * @package Stilero
 * @subpackage class-oauth-fb
 * @author Daniel Eliasson <daniel at stilero.com>
 * @copyright  (C) 2013-dec-18 Stilero Webdesign (http://www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class StileroTwitterResponse{
    
    public $json;
    public $category;
    public $type;
    public $code;
    public $message;
    public $isError;
   
    /**
     * Extracts information from FB responses
     * @param string $json
     */
    public static function handle($json) {
        $response = json_decode($json);
        if(isset($response->errors[0])){
            self::error($response->errors[0]);
        }else{
            if(is_object($response)){
                return $response;
            }else{
                return $json;
            }
       }
    }
    
    /**
     * Handles error responses
     * @param stdClass $response
     */
    protected static function error($response){
        $code = null;
        $message = null;
        if (isset($response->message)){
            $message .= ': '.$response->message;
        }
        
        if (isset($response->code)){
            $code = $response->code;
        }
        JError::raiseError($code, $message);
    }
    
        
}
