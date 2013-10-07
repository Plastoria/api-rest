<?php
/**
 * Created by IntelliJ IDEA.
 * User: Christian
 * Date: 2/10/13
 * Time: 15:45
 * To change this template use File | Settings | File Templates.
 */

include_once __DIR__.'/../vendor/autoload.php';

use Plastoria\Rest\PlastoriaRestClient;

$client=new PlastoriaRestClient();
$client
    ->setLogin('xxxx@xxxx.xx')
    ->setPassword('xxxxxxxx')
    ->setLocale('EN')
    ->setEndPoint('http://api.plastoria.com/api/1.0');

//$resp=$client->dropShipOrder(
//    array(
//        'items'=>array(array(
//            'ref'=>'NS5554 N',
//            'quantity'=>1
//        )),
//        'delivery'=>array(
//            'name'=>'John Does',
//            'company'=>'PLASTORIA',
//            'line1'=>'test l1',
//            'line2'=>'test l2',
//            'region'=>'1',
//            'postalCode'=>'1070',
//            'city'=>'bruxelles',
//            'country'=>'BE',
//            'email'=>'xxx@xxx.be',
//            'phone'=>'444719',
//            'digicode'=>'1234'
//        ),
//        'currency'=>'EUR',
//        'deliveryNotificationEmail'=>'xxx@xxx.be'
//    )
//);
//print_r($resp);

//$client->dropShipConfirmOrder('xxxxxxxxxxxxxxxxxxxxxxxx');

//print_r($client->getProducts(array('count'=>1)));
//print_r($client->getProductPrice('NS5554 N'));
//print_r($client->getProductsInventory());
//print_r($client->getProductAvailableOptions('NS5554 N'));
//print_r($client->getProductTechnicalData('NS5554 N'));