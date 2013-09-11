<?php
namespace che;

class VKException extends \Exception {
    const CURL_NOT_FOUND = 1;
    const CODE_NOT_DEFINED = 2;
    const CURL_ERROR = 3;
    const REQUIRE_PARAMS_AS_ARRAY = 4;

    public static function raise($response) {
        if (!isset($response['result']) && isset($response['code']) && $response['http'] === true) {
            throw new VKServerException("VK API Serverside error", $response['code']);
        } elseif (isset($response['result']['error'])) {
            $error = $response['result']['error'];

            $method_name = 'method.unknown';
            if (isset($error['request_params'])) {
                foreach ($error['request_params'] as $param) {
                    if ($param['key'] == 'method') {
                        $method_name = $param['value'];
                        break;
                    }
                }
            }

            $message = $method_name . ': ' .$error['error_msg'];
            $code    = $error['error_code'];
        } else {
            $message = 'Unknown error';
            $code    = 0;
        }

        throw new VKException($message, $code);
    }
}

class VKServerException extends VKException {}