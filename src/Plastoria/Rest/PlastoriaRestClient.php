<?php
/**
 * Created by IntelliJ IDEA.
 * User: Christian
 * Date: 2/10/13
 * Time: 15:12
 * To change this template use File | Settings | File Templates.
 */

namespace Plastoria\Rest;

/**
 * Class PlastoriaRestClient
 * Initialise by chaining appropriate method calls
 * <code>
 * $client
 *      ->setLogin('your_plastoria@login.xx')
 *      ->setPassword('XXXX')
 *      ->setApiKey('XXXX')
 *      ->setLocale('EN')
 *      ->setEndPoint('http://api.plastoria.com/api/1.0');
 * </code>
 * @package Plastoria\Rest
 */
class PlastoriaRestClient {
    private $userName;
    private $password;
    private $endPoint;
    private $locale;
    private $apiKey;

    /**
     * Change local used for querying "labels"
     * @param $locale
     * @return $this
     */
    public function setLocale($locale){
        $this->locale=$locale;
        return $this;
    }

    /**
     * Set the default user name
     * @param $userName
     * @return $this
     */
    public function setLogin($userName){
        $this->userName=$userName;
        return $this;
    }

    /**
     * Set the password which will be stored in MD5
     * @param $password
     * @return $this
     */
    public function setPassword($password){
        $this->password=md5($password);
        return $this;
    }

    public function setApiKey($apiKey){
        $this->apiKey=$apiKey;
        return $this;
    }

    /**
     * Set the endpoint:
     * <dl>
     *  <dt>TEST/DEV</dt>
     *  <dd>https://api-dev.plastoria.com</dd>
     *  <dt>PRODUCTION</dt>
     *  <dd>https://api.plastoria.com</dd>
     * </dl>
     * @param $endPoint
     * @return $this
     */
    public function setEndPoint($endPoint){
        $this->endPoint=$endPoint;
        return $this;
    }

    /**
     * Generate a one use WSSHeader. Use this to use unimplemented methods.
     * @return string
     */
    public function getWSSEHeader(){
        $created=date('c');
        $nonce=uniqid();
        $secret=base64_encode(sha1($nonce.$created.$this->password, true));
        $header='x-wsse: UsernameToken Username="'.$this->userName.'", PasswordDigest="'.$secret.'", Created="'.$created.'", Nonce="'.base64_encode($nonce).'"';
        return $header;
    }

    /**
     * Call ANY method, low level API
     * @param array $requestArray content as PHP array
     * @param string $path  path to append to end point, shown first in documentation
     * @param string $method One of 'GET','POST', 'PUT', 'DELETE', ...
     * @param null|string $getParams
     * @return array
     */
    public function doRequest($requestArray,$path,$method='GET',$getParams=null){
        $finalUri=$this->endPoint.$path;
        if(isset($getParams) && is_array($getParams)){
            $qStringArray=array();
            foreach($getParams as $key=>$param){
                if(is_array($param)){
                    foreach($param as $val){
                        $qStringArray[]=$key.'[]='.urlencode($val);
                    }
                }else{
                    $qStringArray[]=$key.'='.urlencode($param);
                }
            }
            $finalUri.='?'.implode('&',$qStringArray);
        }
        echo 'Query on '.$finalUri."\n";
        $ch=curl_init($finalUri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        switch($method){
            case 'POST':
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($requestArray));
                break;
            case 'GET':
                curl_setopt($ch,CURLOPT_POST,0);
                break;
        }
        echo $this->getWSSEHeader();
        curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-type: application/json;charset=UTF-8',$this->getWSSEHeader(),'API-KEY: '.$this->apiKey));
        $content=curl_exec($ch);
        $httpCode=curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array('data'=>json_decode($content,true),'status'=>$httpCode);
    }

    /**
     * Retrieves client list of products
     * @param null|array $filters Map of key values. Some keys may accept arrays as value
     * @return array list of products
     */
    public function getProducts($filters=null){
        return $this->doRequest(null,'/products/'.urlencode($this->locale),'GET',$filters);
    }

    /**
     * Retrieves the product current and projected inventory (stock)
     * @return array
     */
    public function getProductsInventory(){
        return $this->doRequest(null,'/products/stocks');
    }

    /**
     * Retrieves the product price columns.
     * @param $productCode
     * @param null $filter
     * @return array
     */
    public function getProductPrice($productCode,$filter=null){
        return $this->doRequest(null,'/products/'.rawurlencode($productCode).'/prices','GET',$filter);
    }

    /**
     * Retrieves available product options and associated price increase
     * @param $productCode
     * @return array
     */
    public function getProductAvailableOptions($productCode){
        return $this->doRequest(null,'/products/'.rawurlencode($productCode).'/'.urlencode($this->locale));
    }

    /**
     * Retrieves product technical data
     * @param $productCode
     * @return array
     */
    public function getProductTechnicalData($productCode){
        return $this->doRequest(null,'/products/'.rawurlencode($productCode).'/'.urlencode($this->locale).'/technicals-data');
    }









    public function dropShipOrder($order){
        return $this->doRequest($order,'/drop-shipping/order','POST');
    }

    public function dropShipConfirmOrder($token){
        return $this->doRequest(array('token'=>$token),'/drop-shipping/order/confirm','POST');
    }

}