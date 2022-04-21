<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


use App\Models\Customers;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    function getTableDatas(Request $request){

        $tableNames = DB::table($request->tableName)->paginate($request->perPage, ['*'], 'page', $request->currentPage);


        return response()->json([
            'tableDatas' => $tableNames,
        ], 200);
}

    public function show()
    {

        $customers = Customers::find(424);
        return $customers;
    }

    public function getTableColumns(Request $request)
    {

        $cols = Schema::getColumnListing($request->tableName);
        $colsWithFields = [];
      
        foreach ($cols as $key => $column) {
            array_push($colsWithFields, [
                'field' => $column,
                'type' => Schema::getColumnType($request->tableName,$column),
                'isChecked' => false
              ]);
        }


       return response()->json([
        'colsWithFields' => $colsWithFields,
        'header' => $cols,
        'status' => 200
    ], 200);
    }

}