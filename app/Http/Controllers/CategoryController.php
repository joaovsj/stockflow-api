<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return response()->json([
            'status' => true,
            'body'   => Category::orderByDesc('created_at')->get()
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
        if($category = Category::create($request->all())){
            
            return response()->json([
                'status' => true,
                'message' => 'Categoria cadastrada como Sucesso!',
                'categoryId' => $category->id
            ]);
        }

        return response()->json([
            'status'=> false,
            'message' => 'Erro ao cadastrar categoria!'
        ]);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $category)
    {
        $categoryData = Category::find($category);

        
        if(isset($categoryData)){

            return response()->json([
                'status' => true,
                'body' => $categoryData, 
            ], 200);
            
            
        }

        return response()->json([
            'status' => false,
            'message' => 'Categoria não encontrada!'
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
        $category = Category::find($id);

        if(isset($category)){

            $result = $category->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Categoria atualizada com sucesso!'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Categoria não encontrada!'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(Category::destroy($id)){
            
            return response()->json([
                'status' => true,
                'messsage' => 'Categoria deletada com sucesso!'
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Categoria não encontrada!'
        ], 404);
    }
}
