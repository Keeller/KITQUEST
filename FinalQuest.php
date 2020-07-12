<?php
require_once "D:\\ops\\OSPanel\\domains\\sobes.ru\\vendor\\autoload.php";
main();
function main(){
    $cfg = require_once "Config.php";
    if ($cfg['isCors']) {
        cors($cfg['origin']);
    }

    $client = new \RetailCrm\ApiClient($cfg['url'], $cfg['apikey'], \RetailCrm\ApiClient::V5);
    $data = json_decode(\FinalQuest\Query::get($cfg["providerUrl"]."=" . ((int)$_GET['id'])), true);
    if ($data['success'] == 1) {
        $result = $data['result'];
        try {
            $id = getCustomerId($client, $result);
            createOrder($client, $result, $id);

        } catch (\RetailCrm\Exception\CurlException $e) {
            echo "Connection error: " . $e->getMessage();
        }


    }
}

function cors($origin) {


    if (isset($_SERVER['HTTP_ORIGIN'])) {

        header("Access-Control-Allow-Origin: {$origin}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
    }


    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

}

function createOrder(\RetailCrm\ApiClient $client,array $result,int $cust_id){

    $response = $client->request->ordersCreate(array(
        'customer'=>['id'=>$cust_id],
        'firstName' => $result['first_name'],
        'lastName' => $result['last_name'],
        'items' => $result['items'],
        'email' =>$result['email']
    ));
    if(passErr($response))
        echo 'Order successfully created. Order ID into retailCRM = ' . $response->id;


}
function passErr(\RetailCrm\Response\ApiResponse $resp): bool {
    if ($resp->isSuccessful() && 201 === $resp->getStatusCode()) {

        return true;

    }
    else {
        echo sprintf(
            "Error: [HTTP-code %s] %s",
            $resp->getStatusCode(),
            $resp->getErrorMsg()
        );
    }


    if (isset($resp['errors'])) {
        print_r($resp['errors']);
    }

    return false;
}
function getCustomerId( \RetailCrm\ApiClient $client,array $result): int{
    $resp=$client->request->customersGet($result['id']);
    $id=0;
    if(!$resp->isSuccessful()){
        $resp=$client->request->customersCreate(array(
            'externalId'=>$result['id']
        ));
        if(passErr($resp))
            $id = $resp->id;

    }
    else{
        $re=$client->request->customersGet($result['id']);
        $id=$re->asJsonResponse()->getResponse()["customer"]["id"];
    }
    return $id;
}