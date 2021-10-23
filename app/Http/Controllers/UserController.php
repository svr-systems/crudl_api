<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = DB::table("users AS u")
                ->select(
                    "u.id",
                    "u.name",
                    "u.email",
                    "r.name AS role_name"
                )
                ->join("roles AS r", "r.id", "=", "u.role_id")
                ->where("u.active", $request->active)
                ->where("u.id", "!=", $request->id)
                ->orderBy("u.name", "asc")
                ->get();

            return response()->json([
                "success" => true,
                "message" => "Listado de registros retornados correctamente",
                "data" => $data,
                "total_rows" => $data->count()
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => "ERR. " . $th
            ], 200);
        }
    }

    public function store(Request $request)
    {
        try {
            //MODIFICATIONS
            $request->merge(["email" => strtolower(trim($request->email))]);

            //VALIDATIONS
            $validator = Validator::make(
                $request->all(),
                [
                    "name" => "string|required|min:2|max:45",
                    "birthday" => "date|required",
                    "email" => "string|required|email|min:6|max:65|unique:users",
                    "password" => "string|required|min:5|max:20",
                    "role_id" => "required"
                ],
                [
                    "email.unique" => "El Correo Electrónico ya ha sido registrado"
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "message" => $validator->errors()->first()
                ], 200);
            }

            //VALIDATION FILE 1
            $avatar_file = $request->file("avatar_file");

            if ($avatar_file) {
                $validator = Validator::make(
                    $request->all(),
                    ["avatar_file" => "image|mimes:jpeg,jpg|max:2048"],
                    ["avatar_file.max" => "El tamaño máximo de la Imagen de perfil es de 2MB"]
                );

                if ($validator->fails()) {
                    return response()->json([
                        "success" => false,
                        "message" => $validator->errors()->first()
                    ], 200);
                }
            }

            //STORE DATA
            $insert = User::create([
                "name" => mb_strtoupper(trim($request->name), "UTF-8"),
                "birthday" => trim($request->birthday),
                "email" => mb_strtolower(trim($request->email), "UTF-8"),
                "password" => bcrypt(trim($request->password)),
                "created_by_id" => $request->created_by_id,
                "updated_by_id" => $request->created_by_id,
                "role_id" => $request->role_id
            ]);

            //UPLOAD FILES
            $path = "public/users/{$insert->id}/avatar";
            Storage::makeDirectory($path);

            if ($avatar_file) {
                $avatar_file->store($path);
            }

            //RETURN SUCCESS
            return response()->json([
                "success" => true,
                "message" => "Registro creado correctamente"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => "ERR. " . $th
            ], 200);
        }
    }

    public function show($id)
    {
        try {
            $data = DB::table("users AS x")
                ->select(
                    "x.*",
                    "r.name AS role_name",
                    "uc.name AS created_at_name",
                    "uu.name AS updated_at_name"
                )
                ->join("roles AS r", "r.id", "=", "x.role_id")
                ->join("users AS uc", "uc.id", "=", "x.created_by_id")
                ->join("users AS uu", "uu.id", "=", "x.updated_by_id")
                ->where("x.id", $id)
                ->get();

            $data = $data[0];

            $path = "public/users/{$id}/avatar";
            $avatar_file = Storage::files($path);

            if (count($avatar_file) > 0) {
                $avatar_file = Str::substr($avatar_file[0], 7);
            } else {
                $avatar_file = "";
            }

            $data->avatar_file = $avatar_file;

            return response()->json([
                "success" => true,
                "message" => "Datos del registro retornados correctamente",
                "data" => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => "ERR. " . $th
            ], 200);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            //MODIFICATIONS
            $request->merge(["email" => strtolower(trim($request->email))]);

            //VALIDATIONS
            $validator = Validator::make(
                $request->all(),
                [
                    "name" => "string|required|min:2|max:45",
                    "birthday" => "date|required",
                    "role_id" => "required",
                    "email" => "string|required|email|min:6|max:65|unique:users,email," . $id,
                ],
                [
                    "email.unique" => "El Correo Electrónico ya ha sido registrado"
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    "message" => $validator->errors()->first()
                ], 200);
            }

            //UPDATE DATA
            User::where("id", $id)->update([
                "name" => mb_strtoupper(trim($request->name), "UTF-8"),
                "birthday" => trim($request->birthday),
                "email" => mb_strtolower(trim($request->email), "UTF-8"),
                "updated_by_id" => $request->updated_by_id,
                "role_id" => $request->role_id
            ]);

            //RETURN SUCCESS
            return response()->json([
                "success" => true,
                "message" => "Registro actualizado correctamente"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => "ERR. " . $th
            ], 200);
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            User::where("id", $id)->update([
                "active" => "0",
                "updated_by_id" => $request->updated_by_id
            ]);
            return response()->json([
                "success" => true,
                "message"   => "Registro eliminado correctamente"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => "ERR. " . $th
            ], 200);
        }
    }
}
