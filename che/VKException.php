<?php
namespace che;

class VKException extends \Exception {
    const CURL_NOT_FOUND = 1;
    const CODE_NOT_DEFINED = 2;
    const CURL_ERROR = 3;
    const REQUIRE_PARAMS_AS_ARRAY = 4;

    public static function raise($response) {
        if (isset($response['result']['error_description'])) {
            $message = $response['result']['error_description'];
            $code    = $response['code'];
        } elseif (isset($response['result']['error']['error_msg'])) {
            $message = $response['result']['error']['request_params'][1]['value'] . ': ' .$response['result']['error']['error_msg'];
            $code    = $response['result']['error']['error_code'];
        } else {
            $message = 'Unknown error';
            $code    = 0;
        }
        
        switch($code) {
          case 400:
            throw new VKBadRequestException($message, $code);
          case 401:
            throw new VKNotAuthorizedException($message, $code);
          case 403:
            throw new VKForbiddenException($message, $code);
          case 404:
            throw new VKNotFoundException($message, $code);
          case 406:
            throw new VKNotAcceptableException($message, $code);
          case 420:
            throw new VKEnhanceYourCalmException($message, $code);
          case 500:
            throw new VKInternalServerException($message, $code);
          case 502:
            throw new VKBadGatewayException($message, $code);
          case 503:
            throw new VKServiceUnavailableException($message, $code);
          default:
            throw new VKException($message, $code);
        }
    }
}

class VKBadRequestException         extends VKException {}
class VKNotAuthorizedException      extends VKException {}
class VKForbiddenException          extends VKException {}
class VKNotFoundException           extends VKException {}
class VKNotAcceptableException      extends VKException {}
class VKEnhanceYourCalmException    extends VKException {}
class VKInternalServerException     extends VKException {}
class VKBadGatewayException         extends VKException {}
class VKServiceUnavailableException extends VKException {}