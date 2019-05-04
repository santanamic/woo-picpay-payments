<?php

require_once(__DIR__ . './config.php');

require_once(dirname(__DIR__ , 4) . '/vendor/autoload.php');

use PicPay\Configuration;

$config = PicPay\Configuration::getDefaultConfiguration()->setApiKey('x-picpay-token', API_KEY);

$apiInstance = new PicPay\SDK\StatusApi(

	new _PA88H63MC84HH6TR4VD\GuzzleHttp\Client([
		'verify'  => PCPY_HTTPS_REQUIRE,
		'headers' => PCPY_CLIENT_HEADERS
	]),
    $config
);

$reference_id = "123456";

try {
    $result = $apiInstance->getStatus($reference_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling StatusApi->getStatus: ', $e->getMessage(), PHP_EOL;
}

?>