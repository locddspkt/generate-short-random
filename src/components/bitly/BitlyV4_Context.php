<?php
namespace GenerateShortRandom;

//with version 3, we use fixed token instead
class BitlyV4_Context {
    private static $access_token = '';
    private static $group_guid = '';

    /**
     * also save the file if not existed before
     * @param $token
     * @return array
     */
    private static function getTokenData($token) {
        $tokensFolder = __DIR__ . '/tokens';
        CommonFunction::checkAndCreateFolder($tokensFolder);
        $tokenFileName = "$tokensFolder/{$token}.php";
        if (!file_exists($tokenFileName)) {
            $content = "<?php return ['group_guid' => ''];";
            file_put_contents($tokenFileName, $content);
        }

        $tokenData = include $tokenFileName;
        return $tokenData;
    }

    private static function setTokenData($token, $groupGuid) {
        $tokensFolder = __DIR__ . '/tokens';
        CommonFunction::checkAndCreateFolder($tokensFolder);
        $tokenFileName = "$tokensFolder/{$token}.php";
        $content = "<?php return ['group_guid' => '$groupGuid'];";
        file_put_contents($tokenFileName, $content);
    }

    /***
     * also get the guid then save file (if not exists)
     * @param $token
     */
    public static function init($token) {
        self::$access_token = $token;
        //also check if this token has been saved or not
        $tokenData = self::getTokenData($token);
        $guid = $tokenData['group_guid'];
        if (empty($guid)) {
            //get the guid then save
            $groups = self::GetGroups();

            if ($groups === false) return;

            $group = reset($groups);
            if (empty($group)) return;

            $guid = $group->guid;

            //also set guid
            self::setTokenData($token, $guid);
        }
        self::setGroupGuid($guid);
    }

    private static function setGroupGuid($guid) {
        self::$group_guid = $guid;
    }

    private static function sendPostRequest($url, $data, $headers = false) {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        if (is_array($headers) && !empty($headers)) {

            //check if has token and headers do not have
            if (self::$access_token !== '') {
                $token = self::$access_token;
                $tokenHeader = "Authorization: Bearer {$token}";

                if (!in_array($tokenHeader, $headers)) {
                    $headers[] = $tokenHeader;
                }
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $payload = http_build_query($data);
        $data = json_encode($data);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }

    private static function sendGetRequest($url, $data, $headers = false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        if (is_array($headers) && !empty($headers)) {
            //check if has token and headers do not have
            if (self::$access_token !== '') {
                $token = self::$access_token;
                $tokenHeader = "Authorization: Bearer {$token}";

                if (!in_array($tokenHeader, $headers)) {
                    $headers[] = $tokenHeader;
                }
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $payload = http_build_query($data);
        curl_setopt($ch, CURLOPT_POST, 0);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);

        curl_close($ch);

        if ($output === false) return false;

        return $output;
    }

    private static function GetGroups() {
        $url = 'https://api-ssl.bitly.com/v4/groups';
        $data = [];
        $headers = [
            'Host: api-ssl.bitly.com',
            'Accept: */*',
            'Content-Type: application/json',
        ];
        $response = self::sendGetRequest($url, $data, $headers);

        if ($response === false) return false;

        $response = json_decode($response);
        return $response->groups;
    }

    public static function ShortenLink($longUrl) {
        if (self::$access_token === '') return false;
        if (self::$group_guid === '') return false;

        $url = 'https://api-ssl.bitly.com/v4/shorten';
        $data = [];

        $data['long_url'] = $longUrl;
        $data['group_guid'] = self::$group_guid;

        $headers = [
            'Host: api-ssl.bitly.com',
            'Content-Type: application/json',
        ];

//        $data = new stdClass();
//        $data->long_url = $longUrl;
//        $data->group_guid = self::$group_guid;


        $response = self::sendPostRequest($url, $data, $headers);

        if ($response === false) return false;

        return $response;
    }
    /*-------------------------------------------*/
}