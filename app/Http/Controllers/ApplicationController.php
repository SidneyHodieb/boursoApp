<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;

class ApplicationController extends Controller
{
  public function index()
  {
      return view('application');
  }

  public function Alltables(){

    $tables = DB::select("SELECT * FROM information_schema.tables WHERE table_catalog = 'dvdrental' and table_schema = 'public';");
    $tables = array_column($tables, 'table_name');

    return response()->json([
      'tables' => $tables,
  ], 200);
  

  }

  public function editTable(Request $request, $tableName){
    
    $data  = DB::table($tableName)->where('id','=',$request->id)->update($request->all());

    return response()->json([
      'tables' => $data,
  ], 200);
  
  }

  public function runQuery(Request $request){
    
     $result = DB::select($request->requete); 
  
     return response()->json([
      'tables' => $result,
  ], 200);

  } 

  public function getDirectories(){
    
    $directories = DB::select("SELECT id, directory_name as name FROM directory");
    $directories =  json_decode(json_encode($directories),true);
    $explorer = [];


    foreach ($directories as $key => $dir) {
      array_push($explorer, [
          'name' => $dir['name'],
          'children' => DB::select("SELECT id, name FROM subdirectory WHERE directory_id=".$dir['id']."" ),
        ]);
     }
 
      return response()->json([
      'directories' => $explorer,
      ], 200);


 }
}
