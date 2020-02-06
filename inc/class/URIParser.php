<?php
/**
 * Created by PhpStorm.
 * User: mrred
 * Date: 11.01.2019
 * Time: 17:33
 */

class URIParser {

    /**
     * Extracts video ID from a link
     *
     * @param string $uri
     * @return bool|string
     */
    public static function getVideoId($uri) {
        $vars = [];
        parse_str( parse_url($uri, PHP_URL_QUERY ), $vars );
        if (isset($vars['v'])) {
            return $vars['v'];
        }
        return false;
    }

    /**
     * Returns a full URI of the best available video quality for provided youtube video
     *
     * @param string $uri
     * @return mixed
     */
    public static function getVideoURI($uri) {
        if (self::getVideoId($uri)) {
            exec('youtube-dl -j '.$uri,$out);
            if (!key_exists(0, $out)) {
                return false;
            }
            $data = json_decode($out[0], true);

            $id = 0;
            $size = 0;
            foreach ($data['formats'] as $i => $format) {
                if ($format['filesize'] > $size) {
                    $id = $i;
                }
            }

            return ['url' => $data['formats'][$id]['url'], 'title' => $data['title']];
        }

        return false;
    }

    /**
     * Generates a globally unique identifier
     * @return string
     */
    public static function generateGUID(){
        if (function_exists('com_create_guid') === true)
            return trim(com_create_guid(), '{}');

        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}