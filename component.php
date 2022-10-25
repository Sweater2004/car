<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Loader;

Loader::IncludeModule("iblock");


$IBLOCK_CAR = 1;

$time_start = strtotime($_GET['input_time_start']);
$time_end = strtotime($_GET['input_time_end']);
$dbUser = \Bitrix\Main\UserTable::getList(array(
	'select' => array('ID', 'NAME', 'WORK_POSITION'),
	'filter' => array('ID' => $USER->GetID()),
));

$arUser = $dbUser->fetch();
switch ($arUser['WORK_POSITION']) {
	case "vip":
		$arFilter = array(
			"IBLOCK_ID" => $IBLOCK_CAR,
			"PROPERTY_CLASS_COMFORT_VALUE" => 'VIP',
		);
		break;
	case "econom":
		$arFilter = array(
			"IBLOCK_ID" => $IBLOCK_CAR,
			"PROPERTY_CLASS_COMFORT_VALUE" => 'ECONOM',
		);
		break;
	case "standart":
		$arFilter = array(
			"IBLOCK_ID" => $IBLOCK_CAR,
			"PROPERTY_CLASS_COMFORT_VALUE" => 'STANDART',
		);
		break;
	case "admin":
		$arFilter = array(
			"IBLOCK_ID" => $IBLOCK_CAR,
			"PROPERTY_CLASS_COMFORT_VALUE" => ['STANDART', 'ECONOM', 'VIP'],
		);
		break;
}

$get_cars  = CIBlockElement::GetList(
	array(),
	$arFilter,
	false,
	false,
	['ID', 'NAME', 'PROPERTY_CLASS_COMFORT', 'PROPERTY_MODEL_CAR', 'PROPERTY_NAME_VOD', 'PROPERTY_CAR_START', 'PROPERTY_CAR_FINISH']
);
$cars_list = [];

while (($car = $get_cars->GetNext()) == true) {
	$cars_list[] = $car;
};


foreach ($cars_list as $key => $car) {
	$start = strtotime($car['PROPERTY_CAR_START_VALUE']);
	$end = strtotime($car['PROPERTY_CAR_FINISH_VALUE']);

	if (($time_start <= $start) and ($time_start <= $end) and ($time_end <= $end) and ($time_end >= $time_start)
	) {
		echo 	"<p>класс комфорта : " . $cars_list[$key]['PROPERTY_CLASS_COMFORT_VALUE'] . "</p>",
		"<p>модель автомобиля:" . $cars_list[$key]['PROPERTY_MODEL_CAR_VALUE'] . "</p>",
		"<p>имя водителя:" . $cars_list[$key]['PROPERTY_NAME_VOD_VALUE'] . "</p>",
		"<p>начало поездки:" . $cars_list[$key]['PROPERTY_CAR_START_VALUE'] . "</p>",
		"<p>конец поездки:" . $cars_list[$key]['PROPERTY_CAR_FINISH_VALUE'] . "</p></br>";
	}
};
$arResult['CARS_LIST'] = $cars_list;

$this->IncludeComponentTemplate();
