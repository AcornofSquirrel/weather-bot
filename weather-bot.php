<?php
#!C:/xampp/php

//ライブラリの読み込み
require_once('C:/xampp/htdocs/php/twitter/twitteroauth/twitteroauth-master/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;

//APIを利用するための情報を入れる

//ファイル参照で取得する
$turl = 'C:\xampp\htdocs\php\config\Twitter_API_Key.json';
$tjson = file_get_contents($turl);
$tjson = mb_convert_encoding($tjson, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$TwitterAPIKey = json_decode($tjson,true);

$consumerKey       = $TwitterAPIKey['user']['consumerKey'];
$consumerSecret    = $TwitterAPIKey['user']['consumerSecret'];
$accessToken       = $TwitterAPIKey['user']['accessToken'];
$accessTokenSecret = $TwitterAPIKey['user']['accessTokenSecret'];


//発言を行うメソッドを指定
$method = "statuses/update";




//接続
$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);


$url = 'http://api.openweathermap.org/data/2.5/forecast?id=1856215&units=metric&APPID=4a89234c69b1d92519f5b3e5a07a7b69';
$weather = json_decode(file_get_contents($url), true);



//三時間ごとの発言の中身
$text = array(
 "三時間後の天気をお伝えします",
 "天気:" . $weather['list'][0]['weather'][0]['main'], 
 "天気詳細:" . $weather['list'][0]['weather'][0]['description'], 
 "温度:" . $weather['list'][0]['main']['temp'] . "度", 
 "湿度:" . $weather['list'][0]['main']['humidity'] . "パーセント", 
 "風速:" . $weather['list'][0]['wind']['speed'] . "メートル",
 "プロフィールの三時間ごとの天気の情報を参考にお伝えしました",
);


foreach ($text as $key) {
 $re = $connection->post($method/*"statuses/update"*/, array(
  "status" => $text[$key + 0] . "\n" . $text[$key + 1] . "\n" .  $text[$key + 2] . "\n" .  $text[$key + 3] . "\n" .  $text[$key + 4] . "\n" . $text[$key + 5] . "\n" . $text[$key + 6],
 ));
}

/*上記の内容を改良中
$retries = 3;
for($count = 0; $count < $retries; $count++) {
	$url = 'http://api.openweathermap.org/data/2.5/forecast?id=1856215&units=metric&APPID=4a89234c69b1d92519f5b3e5a07a7b69';
	$weather = json_decode(file_get_contents($url), true);
	
	$text = array(
	"三時間後の天気をお伝えします",
	"天気:" . $weather['list'][0]['weather'][0]['main'], 
	"天気詳細:" . $weather['list'][0]['weather'][0]['description'], 
	"温度:" . $weather['list'][0]['main']['temp'] . "度", 
	"湿度:" . $weather['list'][0]['main']['humidity'] . "パーセント", 
	"風速:" . $weather['list'][0]['wind']['speed'] . "メートル",
	"プロフィールの三時間ごとの天気の情報を参考にお伝えしました",
	);
	
	//プログラムの文字コードがSJISの場合はUTF-8に変換
	//$text = mb_convert_encoding($text, "UTF-8", "SJIS");
	
	$parameters = array("status" => $text);
	
	$response = $connection->post($method, $parameters);
	$http_info = $connection->http_info;
	$http_code = $http_info["hrrp_code"];
	
	//HTTPコードが200か304で、かつ、エラーメッセージがなければ成功
	if(($http_code == "200" || $http_code == "304") && !array_key_exists("error", $response)) {
		break;
	}
	// 1秒待つ
	sleep(1);
}
*/

$nowtime=getdate();
if ($nowtime['hours'] > 20 ) {
	$tmp_url = "http://weather.livedoor.com/forecast/webservice/json/v1?city=200010";
	$json = file_get_contents($tmp_url,true) or die("Failed to get json");
	$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
	$obj = json_decode($json);

	$min = $obj->forecasts[1]->temperature->min->celsius;
	$max = $obj->forecasts[1]->temperature->max->celsius;
	$telop = $obj->forecasts[1]->telop;

//一日の終わりの発言の中身
	$tomorrow_text = array(
	"続いて、明日の天気をお伝えします",
	"明日の天気:" . $telop, 
	"明日の最高気温:" . $max . "度", 
	"明日の最低気温:" . $min . "度", 
	"プロフィールの明日の天気の情報を参考にお伝えしました",
	"それではみなさん、おやすみなさい"
	);

	foreach ($tomorrow_text as $tomorrow_key) {
	$re = $connection->post($method/*"statuses/update"*/, array(
	"status" => $tomorrow_text[$tomorrow_key + 0] . "\n" . $tomorrow_text[$tomorrow_key + 1] . "\n" .  $tomorrow_text[$tomorrow_key + 2] . "\n" .  $tomorrow_text[$tomorrow_key + 3] . "\n" .  $tomorrow_text[$tomorrow_key + 4] . "\n" .  $tomorrow_text[$tomorrow_key + 5],
	));
	}
}
?>
