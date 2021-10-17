<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = DB::table($request->name)
                ->orderBy($request->no_order === "true" ? "id" : "name", "asc")
                ->get();
            return response()->json([
                "success" => true,
                "message" => "Registros del catálogo \"{$request->name}\" leidos correctamente",
                "data" => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => "ERR. " . $th
            ], 200);
        }
    }
}
