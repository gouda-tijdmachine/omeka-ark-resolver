<?php

define('IDENTIFIER_PROPERTY',1082);
define('OMEKA_S_PATH','/omeka/');
define('SITE_PATH','/data/');
define('DATABASE_INI','../config/database.ini');

$location='';
if (isset($_GET["ark"])) {
    $location=resolve_ark("https://n2t.net/".$_GET["ark"]);
}

if (!empty($location)) {
	if (isset($_SERVER['HTTP_ACCEPT']) && !empty($_SERVER['HTTP_ACCEPT'])) {
		header('Accept: '.$_SERVER['HTTP_ACCEPT']);
	}
	header("Location: ".$location);
} else {
	header("HTTP/1.0 404 Not Found");
}
exit;


function resolve_ark($ark) {
    return resolve_value_property($ark,IDENTIFIER_PROPERTY);
}


function resolve_value_property($value,$property_id) {
	$database_settings=parse_ini_file(DATABASE_INI);
	try {
		$mysqli = new mysqli($database_settings["host"], $database_settings["user"], $database_settings["password"], $database_settings["dbname"]);
	} catch (\mysqli_sql_exception $e) {
		throw new \mysqli_sql_exception($e->getMessage(), $e->getCode());
	}

    $sql="SELECT r.resource_type, r.id FROM value v, resource r WHERE v.resource_id=r.id AND v.value=? AND v.property_id=".$property_id;
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s",$value);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result) {
		if ($result["resource_type"]=="Omeka\Entity\ItemSet") {
			return OMEKA_S_PATH."s".SITE_PATH."item-set/".$result["id"];
		} else {
			return OMEKA_S_PATH."s".SITE_PATH."item/".$result["id"];
		}
    }
}
