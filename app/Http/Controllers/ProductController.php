<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Product;

use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $name     = request('name')     ?? "";
        $category = request('category') ?? "";        

        $products = DB::table('products as p')
            ->select('p.*', 'c.name as category', 'prov.name as provider')
            ->crossJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->crossJoin('providers as prov', 'prov.id', '=','p.provider_id')
            ->where('p.disabled', false)
            ->where('p.name', 'like', "%$name%")
            ->where('c.name', 'like', "%$category%")
            ->orderBy('updated_at', 'desc')
            ->get()->toArray();
        

        return response()->json([
            'status' => true,
            'body'   => $products
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
        if(Product::create($request->all())){
            
            return response()->json([
                'status' => true,
                'message' => 'Produto cadastrado como Sucesso!'
            ]);
        }

        return response()->json([
            'status'=> false,
            'message' => 'Erro ao cadastrar Produto!'
        ]);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = DB::table('products as p')
            ->select('p.*', 'c.name as category', 'prov.name as provider')
            ->crossJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->crossJoin('providers as prov', 'prov.id', '=','p.provider_id')
            ->where('p.id', '=', $id)
            ->orderBy('created_at', 'desc')
            ->get()->first();

    
        if(isset($product)){

            return response()->json([
                'status' => true,
                'body' => $product, 
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Produto não encontrado!'
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
        $product = Product::find($id);

        if(isset($product)){

            $result = $product->update($request->all());
            return response()->json([
                'status' => $result,
                'message' => 'Produto atualizado com sucesso!'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Produto não encontrado!'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(Product::destroy($id)){
            
            return response()->json([
                'status' => true,
                'messsage' => 'Produto deletado com sucesso!'
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Produto não encontrado!'
        ], 404);
    }

    public function deleteAll(Request $request){

        $items  = $request->all();
        $status = true;

        for ($i=0; $i < count($items); $i++) { 
            
            $product = DB::table('products')
                            ->where('id', $items[$i])
                            ->update(['disabled' => true]); 

            if(!$product){
                $status  = false;
                break;
            }
        }

        if($status){
            return response()->json([
                'status'    => true, 
                'message'   => "Produtos deletados com sucesso!"
            ], 200);     
        }

        return response()->json([
            'status'    => false, 
            'message'   => "Erro ao realizar operação!"
        ], 400);     
    
    }
}
