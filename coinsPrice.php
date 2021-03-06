<?php
/**
 * COINSPRICE USD FUNCTION
 * ************************
 * 
 * Author : Gabriel KALALA | gabrielkalala@protonmail.com |  https://www.linkedin.com/in/gabrielkalala/
 * Using Binance API.
 * https://binance-docs.github.io/apidocs/spot/en/#symbol-price-ticker
 * Examples
 * https://syncwith.com/api/binance/get/api-v3-ticker-price
 * The price is showing in US Dollars.
 * The GET request is bellow:
 * //$link = "https://api.binance.com/api/v3/ticker/price?symbol=BTCUSDT";
 * 
 */
// To add a new coin, please add it to the $symbols array.
// Add the symbol coins + USDT.
// example to add SOL, add SOLUSDT.
$symbols = array("BTCUSDT", "ETHUSDT", "LTCUSDT", "BNBUSDT", "DOTUSDT", "DASHUSDT");

// Main Fuction
function coinsPrice($symbols){
    try {
        $coins = array();
        foreach($symbols as $symbol){
            $link = "https://api.binance.com/api/v3/ticker/price?symbol=".$symbol."";


            /*** file_get_contents NOT RECOMMENDED ***
            $response = file_get_contents($link);
            if ($response !== false) {
                //parse to JSON
                $responseJSON = json_decode($response, TRUE);
                $s = formatSymbol($responseJSON['symbol']);
                $p = formatPrice($responseJSON['price']);

                $coins[] = array('symbol'=>$s, 'price'=>$p);
            }
            */

            $curl = curl_init($link);
            //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // desactiver la verification https  ... only in local  (NOT RECOMMENDED)

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, __DIR__ . DIRECTORY_SEPARATOR . "binanceCertificate.cer");

            //curl_setopt($curl, CURLOPT_CAINFO, __DIR__.DIRECTORY_SEPARATOR."binanceCertificate.cer");

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($curl);

            if ($data === false){ // si erreur
                var_dump(curl_error($curl));
            } else{
                //parse to JSON
                $data = json_decode($data, TRUE);
                $s = formatSymbol($data['symbol']);
                $p = formatPrice($data['price']);

                $coins[] = array('symbol' => $s, 'price' => $p);
            }
            curl_close($curl);
        }
        // only if you want to return an array
        return $coins;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

// Remove USDT in the coin'name
function formatSymbol($string)
{
    //remove USDT = 4 last characters
    $string = substr($string, 0, -4);
    return $string;
}
// Round price to 2 decimal
function formatPrice($number)
{
    return number_format((float)$number, 2, '.', ' ');
}
// read coins
function readCoins($coins)
{
    foreach ($coins as $coin) {
        echo $coin['symbol'] . ' : ' . $coin['price'] . ' $ <br/>';
    }
}
// create Json file
function createJSONFile($coins){
    $JSON = json_encode($coins);
    $file_name = 'coinsPrice.json'; 
    file_put_contents($file_name, $JSON);
}
// read JSON FILE
function readJSONFile($coins){
    $data = file_get_contents($coins);
    $data = json_decode($data, true);
    foreach($data as $coin){
        echo $coin['symbol'] . ' : ' . $coin['price'] . ' $ <br/>';
    }
    echo 'readJSONFILE';
}
$coins = coinsPrice($symbols);
createJSONFile($coins);
$file_name = 'coinsPrice.json';
readJSONFile($file_name);
?>