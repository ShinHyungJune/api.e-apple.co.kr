<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';*/

Route::get('test', function () {
    $result = DB::select(" SELECT COLUMN_NAME, COLUMN_COMMENT FROM information_schema.columns WHERE TABLE_SCHEMA = 'fruittree' AND TABLE_NAME = 'product_inquiries' ");
    //dd($result);
    //bodyParameters
    //'name' => ['description' => '<span class="point">상품명</span>'],

    $responses = '{';
    $bodyParameters = "[\n";
    foreach ($result as $key => $value) {
        $responses .= '"' . $value->COLUMN_NAME . '" : "' . $value->COLUMN_COMMENT . '",';
        $bodyParameters .= "'" . $value->COLUMN_NAME . "' => ['description' => '<span class=\"point\">" . $value->COLUMN_COMMENT . "</span>'],\n";
    }
    $responses .= '}';
    $bodyParameters .= ']';

    //echo($responses);
    //echo("<hr/>");
    echo('<textarea style="width:100%;height:100%;">');
    print_r($bodyParameters);
    echo('</textarea>');

});
