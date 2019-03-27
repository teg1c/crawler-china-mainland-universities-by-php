<?php 
require './vendor/autoload.php';
use PL\Whttp;
$urls = [];
for ($i=0; $i <=137 ; $i++) { 
	$urls[] = 'https://gaokao.chsi.com.cn/sch/search.do?searchType=1&start='.(20*$i);
}
$news = [];
try {
	$data = Whttp::get($urls)->getGany(function($data){
	if(!$data['error']){
		preg_match('@<div class="yxk-table">(.*?)</div>@s', $data['body'],$match);
		$data = get_td_array($match[1]);
		$result = [];
		foreach ($data as $key => $value) {
			if (isset($value[7])) {
				$result[trim($value[1])][] = trim($value[0]);
			}
		}
		return $result;
	} 
	throw new Exception($data['error'], 400);
	
	
});
} catch (\Exception $e) {
	echo '请重试:'.$e->getMessage();
	die;
}catch (\Error $e) {
	echo '请重试:'.$e->getMessage();
	die;
}

$result = [];
foreach ($data as $key => $value) {
	foreach ($value as $k => $v) {
		$result[$k][] = $v;
	}
}
foreach ($result as $key => &$value) {
	$value = array_reduce($value, 'array_merge', array());
}
file_put_contents('./china_mainland_universities.json',json_encode($result));
echo '成功';

 ?>