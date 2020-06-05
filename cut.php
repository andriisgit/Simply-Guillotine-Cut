<?php
require_once 'Map2.php';
require_once 'Build.php';

// just for testing purposes
$bar = ['H' => 146, 'W' => 446];
$zajavki = '126*23,80*231,110*211,131*207,127*212,111*171,188*101,129*237';


if (isset($_POST['barh']) && isset($_POST['barh'])) {
    $bar = ['H' => $_POST['barh'], 'W' => $_POST['barw']];
}
if (isset($_POST['zajavki'])) {
    $zajavki = $_POST['zajavki'];
}

if (isset($bar) && isset($zajavki)) {

    $cut = new Cut\Map2($bar);
    
	$map = $cut->mapIt($zajavki);

	if (function_exists('is_iterable')) {
		$clew = is_iterable($map);
	} else {
		$clew = is_array($map);
	}
	// if $map is iterable, was returned array of positioned elements,
    // if number - was returned the id of element bigger than the bar
    // if false - bar size error
	if ($clew) {
        $data = Cut\Build::getHtml(['bar' => $bar, 'map' => $map]);
        $data .= '<div style="position:absolute;top:' . $bar['H'] . 'px;">Used ' . $cut->countUsedElements() . ' elements.</div>';
    } elseif (is_numeric($map)) {
        $data = Cut\Build::getSizeErrHtml($map);
    } elseif ($map === false) {
        $data = Cut\Build::getBarErrHtml();
    }

    if (isset($data)) {
        echo json_encode($data);
    }

}
