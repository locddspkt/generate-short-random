<?php

namespace GenerateShortRandom;


include_once __DIR__ . '/CommonFunction.php';
include_once __DIR__ . '/components/bitly/BitlyV4_Context.php';

/***
 * Class Icons
 * change: save cache icon for each session
 * @package FaLoader
 */
class Generator {
    private static $tokens = [];
    private static $baseUrl = 'http://example.com';

    public static function initTokens($tokens) {
        self::$tokens = $tokens;
        //also set token for all
    }

    private static function getRandomUrl() {
        $guid = CommonFunction::guid();
        $baseUrl = self::$baseUrl;
        $url = "{$baseUrl}/{$guid}";
        return $url;
    }

    public static function random() {
        if (empty(self::$tokens)) return false;

        //set random tokens to get the API
        $tokenIndex = array_rand(self::$tokens);
        BitlyV4_Context::init(self::$tokens[$tokenIndex]);

        $guid = CommonFunction::guid();
        $responseShorten = BitlyV4_Context::ShortenLink(self::getRandomUrl());

        $data = json_decode($responseShorten);
        if (empty($data->id)) return false;
        $id = str_replace('bit.ly/','',$data->id);
        return $id;
    }
}