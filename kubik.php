<?php
date_default_timezone_set('Asia/Jakarta');
require_once("sdata-modules.php");
/**
 * @Author: Eka Syahwan
 * @Date:   2017-12-11 17:01:26
 * @Last Modified by:   CossaMix
 * @Last Modified time: 2018-08-17 15:13:34
*/


##############################################################################################################
$config['deviceCode'] 		= '863525038864155';
$config['tk'] 				= 'ACAe91TASAtKrJzrFiWIjV_6Nt7q-sHPmQdxdHRodw';
$config['token'] 			= '99ddi6xxj5XpfXrnOxvgamdnwsGyBleReIHaQk_DD-UEyrCLMQSimwOrERf9oLxbBfYu03Oi0DTpGFY';
$config['uuid'] 			= '9afc0614d532489ab6a4536f420c231e';
$config['sign'] 			= 'd2f813f1af711688d84b1e0c84743fa9';
$config['android_id'] 		= '64708f320c34e631';
##############################################################################################################


for ($x=0; $x <1; $x++) { 
	$url 	= array(); 
	for ($cid=0; $cid <20; $cid++) { 
		for ($page=0; $page <10; $page++) { 
			$url[] = array(
				'url' 	=> 'http://pre.api.beritaqu.net/content/getList?cid='.$cid.'&page='.$page,
				'note' 	=> 'optional', 
			);
		}
		$ambilBerita = $sdata->sdata($url); unset($url);unset($header);
		foreach ($ambilBerita as $key => $value) {
			$jdata = json_decode($value[respons],true);
			foreach ($jdata[data][data] as $key => $dataArtikel) {
				$artikel[] = $dataArtikel[id];
			}
		}
		$artikel = array_unique($artikel);
		echo "[√] Mengambil data artikel (CID : ".$cid.") ==> ".count(array_unique($artikel))."\r\n";
	}
	while (TRUE) {
		$timeIn60Minutes = time() + 60*60;
		$rnd 	= array_rand($artikel); 
		$id 	= $artikel[$rnd];
		$url[] = array(
			'url' 	=> 'http://pre.api.beritaqu.net/timing/read',
			'note' 	=> $rnd, 
		);
		$header[] = array(
			'post' => 'OSVersion=8.0.0&android_channel=google&android_id='.$config['android_id'].'&content_id='.$id.'&content_type=1&deviceCode='.$config['deviceCode'].'&device_brand=OPPO&device_ip=140.213.10.23.'.rand(0,255).'&device_version=A37f&dtu=001&lat=0.0&lon=0.0&network=3g&pack_channel=google&time='.$timeIn60Minutes.'&tk='.$config['tk'].'&token='.$config['token'].'&uuid='.$config['uuid'].'&version=10047&versionName=1.4.7&sign='.$config['sign'], 
		);
		$respons = $sdata->sdata($url , $header); 
		unset($url);unset($header);
		foreach ($respons as $key => $value) {
			$rjson = json_decode($value[respons],true);
			echo "[+][".$id." (Live : ".count($artikel).")] Message : ".$rjson['message']." | Poin : ".$rjson['data']['amount']." | Read Second : ".$rjson['data']['current_read_second']."\r\n";
			if($rjson[code] == '-20003' || $rjson['data']['current_read_second'] == '330' || $rjson['data']['amount'] == 0){
				unset($artikel[$value[data][note]]);
			}
		}
		if(count($artikel) == 0){
			sleep(30);
			break;
		}
		sleep(20);
	}
	$x++;
} 
