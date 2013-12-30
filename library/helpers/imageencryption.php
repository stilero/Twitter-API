<?php
/**
 * Image Encryption Class
 *
 * @version  1.0
 * @package Stilero
 * @subpackage Twitter-API
 * @author Daniel Eliasson <daniel at stilero.com>
 * @copyright  (C) 2013-dec-30 Stilero Webdesign (http://www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class StileroImageencryption{
    
    /**
     * Encrypts an image using base64 for sending in post requests
     * @param string $imagefile Full path to the image file
     * @return string Encoded image or null if file was not found
     */
    public static function base64Encode($imagefile){
        if( !($str = file_get_contents( $imagefile ))){
            return NULL;
        }
        $strEncoded = base64_encode($str);
        return $strEncoded;
    } 
}
