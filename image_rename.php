<?php

function correct_filename($filename){
	$ru = explode('-', "А-а-Б-б-В-в-Ґ-ґ-Г-г-Д-д-Е-е-Ё-ё-Є-є-Ж-ж-З-з-И-и-І-і-Ї-ї-Й-й-К-к-Л-л-М-м-Н-н-О-о-П-п-Р-р-С-с-Т-т-У-у-Ф-ф-Х-х-Ц-ц-Ч-ч-Ш-ш-Щ-щ-Ъ-ъ-Ы-ы-Ь-ь-Э-э-Ю-ю-Я-я"); 
	$en = explode('-', "A-a-B-b-V-v-G-g-G-g-D-d-E-e-E-e-E-e-ZH-zh-Z-z-I-i-I-i-I-i-J-j-K-k-L-l-M-m-N-n-O-o-P-p-R-r-S-s-T-t-U-u-F-f-H-h-TS-ts-CH-ch-SH-sh-SCH-sch---Y-y---E-e-YU-yu-YA-ya");

	$res = str_replace($ru, $en, $filename);
	$res = preg_replace("/[\s]+/ui", '-', $res);
	$res = preg_replace("/[^a-zA-Z0-9\.\-\_]+/ui", '', $res);
	$res = strtolower($res);
	return $res;  
}

require_once('api/Simpla.php');

$simpla = new Simpla();

$query = $simpla->db->query('SELECT * FROM __images');
$images = $simpla->db->results();

$DIR = dirname(__FILE__).'/files/originals';


$done = 0;
foreach($images as $img){
	$new_name = correct_filename($img->filename);
	
	if($new_name !== $img->filename){
		if(file_exists($DIR.'/'.$img->filename)){
		
			if(rename($DIR.'/'.$img->filename, $DIR.'/'.$new_name)){
				$simpla->db->query('UPDATE __images SET filename=? WHERE id=?', $new_name, $img->id);
				
				$done++;
			}
			
		
		}else{
			echo 'Not find (prid:'.$img->product_id.'): /files/originals/'.$img->filename."\n";
		
		} 
	
	
	}

}

echo "DONE: {$done}\n";
echo "ALL: ".count($images)."\n";


//Чистим кеш
$rem = glob(dirname(__FILE__).'/files/products/*.jpg');
foreach($rem as $d){
	unlink($d);
}
echo "END";
