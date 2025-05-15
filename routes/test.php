<?php

// http://localhost:8000/codes/rebuild
/*Route::get('codes/rebuild', function () {
    Code::rebuild(0, 0);
});*/

/**
 * attributes
 * SELECT
 * -- TABLE_NAME AS 테이블명,
 * -- COLUMN_NAME, COLUMN_COMMENT
 * CONCAT('\'', COLUMN_NAME, '\'=>\'', COLUMN_COMMENT, '\',')
 * FROM
 * information_schema.COLUMNS
 * WHERE
 * TABLE_SCHEMA = 'fruittree'
 * and COLUMN_COMMENT != ''
 * GROUP BY  COLUMN_NAME;
 */

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('body_parameters', function (Request $request) {
    $table = $request->input('table');
    $result = DB::select(" SELECT COLUMN_NAME, COLUMN_COMMENT FROM information_schema.columns
        WHERE TABLE_SCHEMA = 'fruittree' AND TABLE_NAME = ? ", [$table]);
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

Route::get('payment_test', function () {
    $order = Order::with(['orderProducts.productOption.product'])->findOrFail(10);
    /*$order->orderProducts()->map(function ($e) {
        $e->load('product');
    });*/
    return view('payment_test', compact('order'));
});
