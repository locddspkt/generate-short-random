<?php

namespace GenerateShortRandom;

class CommonFunction {
    /***
     *
     * <begin something>content here<end something> --> return content here (index = 0)
     *
     * @param $content
     * @param $begin
     * @param $end
     * @param $index
     * @return string content between
     */
    public static function getContentBetween2Patterns($content, $begin, $end, $index = 0) {
        $items = [];
        $separator = $begin;
        $resultElements = explode($separator, $content);

        for ($i = 1; $i < count($resultElements); $i++) {
            $separator = $end;
            if ($end === '') {
                $items[] = $resultElements[$i];
            }
            else {
                $subItem = explode($separator, $resultElements[$i])[0];
                $items[] = $subItem;
            }

        }

        if (isset($items[$index])) {
            return $items[$index];
        }
        else {
            return false;
        }
    }

    /***
     * the same as get content but get from first and last end
     * @param $content
     * @param $begin
     * @param $end
     * @param int $index
     * @return bool|mixed
     */
    public static function getFullContentBetween2Patterns($content, $begin, $end) {
        $items = [];
        $separator = $begin;
        $resultElements = explode($separator, $content);

        if (empty($resultElements)) return '';

        $separator = $end;
        if ($end === '') {
            $items[] = $resultElements[1];
        }
        else {
            $subContent = $resultElements[1];
            $pos = strrpos($subContent,$separator);
            if ($pos === false) return $subContent; // not found the end
            $newContent = substr($subContent,0,$pos);
            return $newContent;
        }
    }

    public static function checkAndCreateFolder($folder) {
        if (!file_exists($folder)) {
            //create folder
            mkdir($folder);
        }
    }

    public static function guid($hasHyphens = true, $hasBraces = false) {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        }
        else {
            mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = $hasHyphens ? chr(45) : '';// "-"
            $braceStart = $hasBraces ? chr(123) : '';
            $braceEnd = $hasBraces ? chr(125) : '';
            if ($hasHyphens == false) {
                $hyphen = '';
            }
            $uuid = $braceStart// "{"
                . substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12)
                . $braceEnd;// "}"
            return $uuid;
        }
    }
}