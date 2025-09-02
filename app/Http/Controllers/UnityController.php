<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Unity;

class UnityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => true,
            'body'   => Unity::orderByDesc('created_at')->get()
        ]);
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
        if(Unity::create($request->all())){
            
            return response()->json([
                'status' => true,
                'message' => 'Unidade de medida cadastrada com Sucesso!'
            ]);
        }

        return response()->json([
            'status'=> false,
            'message' => 'Erro ao cadastrar unidade de medida!'
        ]);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $unity = Unity::find($id);

        
        if(isset($unity)){

            return response()->json([
                'status' => true,
                'body' => $unity, 
            ], 200);
            
            
        }

        return response()->json([
            'status' => false,
            'message' => 'Unidade de medida não encontrada!'
        ], 404);

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
        $unity = Unity::find($id);

        if(isset($unity)){

            $result = $unity->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Unidade de medida atualizada com sucesso!'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Unidade de medida não encontrada!'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(Unity::destroy($id)){
            
            return response()->json([
                'status' => true,
                'messsage' => 'Unidade de medida deletada com sucesso!'
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Unidade de medida não encontrada!'
        ], 404);
    }
}
