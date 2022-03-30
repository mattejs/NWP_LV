<?php

    $xml = simplexml_load_file('LV2.xml') or die("Cannot find XML file");

    $list = $xml->record;

    foreach($list as $record) {
        echo '<div style="border: 2px solid #0000FF; padding: 5px">' .    
        '<b>Slika</b>: <img src="' . $record->slika . '"/><br>' .
        '<b>Ime</b>: ' . $record->ime . '<br>' .
        '<b>Prezime</b>: ' . $record->prezime . '<br>' .
        '<b>Email</b>: ' . $record->email . '<br>' .
        '<b>Životopis</b>: ' . $record->zivotopis . '<br>' .
        '</div><br>';
    }
?>