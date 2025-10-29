<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $usuarios = User::all();
            return response()->json($usuarios);
        }
        

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

       public function store(Request $request)
    {
        // Validación para la tabla 'usuario'
        $validatedData = $request->validate([
            'correo_electronico' => 'required|email|unique:usuario,correo_electronico',
            'nombre_usuario'     => 'required|string|max:255|unique:usuario,nombre_usuario',
            'password'           => 'required|string|min:8|confirmed',
            'tipo_permiso'       => 'required|in:administrador,medico,recepcionista',
            'estado'             => 'required|in:activo,inactivo', // Ahora usamos strings porque el mutator se encarga
        ]);
        
        // Crear usuario en la tabla 'usuario'
        $usuario = User::create([
            'correo_electronico' => $validatedData['correo_electronico'],
            'nombre_usuario'     => $validatedData['nombre_usuario'],
            'password'           => Hash::make($validatedData['password']),
            'tipo_permiso'       => $validatedData['tipo_permiso'],
            'estado'             => $validatedData['estado'], // El mutator convertirá a entero
        ]);

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'usuario' => $usuario
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
