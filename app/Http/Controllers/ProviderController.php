<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Provider;
use Illuminate\Support\Facades\DB;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => true,
            'body'   => Provider::orderBy('name', 'asc')->get()
        ]);
    }

    public function searchItems(Request $request){
        $fields = $request->all();

        return response()->json([
            'status' => true,
            'body' => Provider::where([
                            ['name', 'like', '%'.$fields['name'].'%'],
                            ['document', 'like', '%'.$fields['document'].'%']
                        ])->orderBy('name', 'asc')->get()
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

        $provider = $request->get('provider');
        $provider['disabled'] = false;

        $provider = Provider::create($provider);

        if($provider){
            
            $address = $request->get('address');
            $address['provider_id'] = $provider->id;
            $address['created_at'] = date('Y-m-d H:i:s');

            if(DB::table('providersAddress')->insert($address)){
                return response()->json([
                    'status' => true,
                    'message' => 'Fornecedor cadastrado como Sucesso!'
                ]);
            }
        }

        return response()->json([
            'status'=> false,
            'message' => 'Erro ao cadastrar fornecedor!'
        ]);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $provider = Provider::find($id);
        $address = DB::table('providersAddress')->where('provider_id', $id)->get()->first();
        
        if(isset($provider)){

            return response()->json([
                'status' => true,
                'body' => ['provider' => $provider, 'address' => $address], 
            ], 200);
            
            
        }

        return response()->json([
            'status' => false,
            'message' => 'Fornecedor não encontrado!'
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
        $provider = Provider::find($id);
        
        if(isset($provider)){
            $result = $provider->update($request->get('provider'));
            $address = DB::table('providersAddress')->where('provider_id', $id)->update($request->get('address'));
            return response()->json([
                'status' => true,
                'message' => 'Fornecedor atualizado com sucesso!'
            ], 200);
            
            
        }

        return response()->json([
            'status' => false,
            'message' => 'Fornecedor não encontrado!'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(Provider::destroy($id)){
            
            return response()->json([
                'status' => true,
                'messsage' => 'Fornecedor deletado com sucesso!'
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Fornecedor não encontrado!'
        ], 404);
    }

    public function deleteAll(Request $request){

        $items  = $request->all();
        $status = true;

        for ($i=0; $i < count($items); $i++) { 
            
            $provider = DB::table('providers')
                            ->where('id', $items[$i])
                            ->update(['disabled' => true]); 

            if(!$provider){
                $status  = false;
                break;
            }
        }

        if($status){
            return response()->json([
                'status'    => true, 
                'message'   => "Fornecedores deletados com sucesso!"
            ], 200);     
        }

        return response()->json([
            'status'    => false, 
            'message'   => "Erro ao realizar operação!"
        ], 400);     
    
    }
}
