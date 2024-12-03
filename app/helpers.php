<?php

use Illuminate\Support\Facades\DB;

function getScribeResponseFile($results, $table, $comments = null)
{
    $columns = collect(DB::select(" SELECT COLUMN_NAME, COLUMN_COMMENT FROM information_schema.columns WHERE TABLE_SCHEMA = 'fruittree' AND TABLE_NAME = ? ", [$table]))
        ->pluck('COLUMN_COMMENT', 'COLUMN_NAME');

    $responseFile = [];
    foreach ($results as $key => $value) {
        $responseFile[$key] = !empty($columns[$key]) ? $columns[$key] : ($comments[$key] ?? $value);
    }

    return $responseFile;
}
