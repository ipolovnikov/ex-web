<?php

// Kickstart the framework
$f3=require('lib/base.php');

$f3->set('DEBUG',1);

$f3->route('GET /',
	function($f3) {
		include("ui/map.htm");
	}
);

$f3->route('GET /get',
	function($f3) {
		// Формирование ответа в виде XML-файла
		header("Content-type: text/xml");
		// Создание XML-файла и родительского элемента
		$dom = new DOMDocument("1.0");
		$node = $dom->createElement("markers");
		$parnode = $dom->appendChild($node);
		
		$file = file_get_contents('db/data.json');			  
		$data = json_decode($file,TRUE);					
		unset($file);

		foreach($data as $row){	

			$node = $dom->createElement("marker"); // Добавление нового узла в XML
			$newnode = $parnode->appendChild($node);
			$newnode->setAttribute("imei", $row['imei']);
			$newnode->setAttribute("phone", $row['phone']);
			$newnode->setAttribute("lat", $row['lat']);
			$newnode->setAttribute("lng", $row['lng']);
			$newnode->setAttribute("datetime", $row['datetime']);
		}
		
		unset($data);

		// Вывод XML-файла на экран (возврат ответа в виде текста)
		echo $dom->saveXML();
	}
);

$f3->route('GET /add',
	function($f3) {
	
		// add?imei=358861060765040&phone=380688146682&lat=50.2822168&lng=28.6252064
	
		if (!$f3->get('GET.lat')) return 0;
		if (!$f3->get('GET.lng')) return 0;
		if (!$f3->get('GET.imei')) return 0;
		if (!$f3->get('GET.phone')) return 0;
		
		$file = file_get_contents('db/data.json');			  
		$data = json_decode($file,TRUE);					
		unset($file);
		
		$data[$f3->get('GET.imei').'_'.$f3->get('GET.phone')] = array(
			'lng'=>$f3->get('GET.lng'),
			'lat'=>$f3->get('GET.lat'), 
			'imei'=>$f3->get('GET.imei'),
			'phone'=>$f3->get('GET.phone'),
			'datetime' => time()
			);
		file_put_contents('db/data.json',json_encode($data));			  
		unset($data);
	}
);

$f3->route('GET /clear',
	function($f3) {
	
		$file = file_get_contents('db/data.json');			  
		$data = json_decode($file,TRUE);					
		unset($file);
		$data = array();
		file_put_contents('db/data.json',json_encode($data));			  
		unset($data);
	}
);

$f3->run();
