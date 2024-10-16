<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Movement;
use Illuminate\Support\Facades\DB;


class MovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $moviment = DB::table('movements as m')
            ->select('m.*', 'p.name as product', 'u.name as user')
            ->orderBy('created_at', 'desc')
            ->crossJoin('users as u', 'u.id', '=', 'user_id')
            ->crossJoin('products as p', 'p.id', '=', 'product_id')
            ->get()->toArray();

        return response()->json([
            'status' => true,
            'body'   => $moviment
        ]);
    }

    /**
     * Display a listing of the resource Entry.
     */
    public function showEntry()
    {
        $moviment = DB::table('movements as m')
            ->select('m.*', 'p.name as product', 'u.name as user')
            ->orderBy('created_at', 'desc')
            ->crossJoin('users as u', 'u.id', '=', 'user_id')
            ->crossJoin('products as p', 'p.id', '=', 'product_id')
            ->where('m.type', '=', 'E')
            ->get()->toArray();

        return response()->json([
            'status' => true,
            'body'   => $moviment
        ]);
    }

    /**
     * Display a listing of the resource Out.
     */
    public function showOut()
    {
        $moviment = DB::table('movements as m')
            ->select('m.*', 'p.name as product', 'u.name as user')
            ->orderBy('created_at', 'desc')
            ->crossJoin('users as u', 'u.id', '=', 'user_id')
            ->crossJoin('products as p', 'p.id', '=', 'product_id')
            ->where('m.type', '=', 'S')
            ->get()->toArray();

        return response()->json([
            'status' => true,
            'body'   => $moviment
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

        $userId = $this->getUserId($request['user_id']);

        $productId      = $request['product_id'];
        $typeOperation  = $request['type'];
        $quantity       = $request['quantity'];
        
        $result = $this->verifyType($productId, $typeOperation, $quantity);
        
        if($result['status'] == false){

            return response()->json([
                'status' => false,
                'message' => $result['message']
            ]);

        }

        $request['user_id'] = base64_decode($request['user_id']);

        if(Movement::create($request->all())){
            
            return response()->json([
                'status' => true,
                'message' => 'Movimentação cadastrada como Sucesso!'
            ]);
        }

        return response()->json([
            'status'=> false,
            'message' => 'Erro ao cadastrar Movimentação!'
        ]);
        
    }

    /**
     * Verify the type of operation and update the database
     * 
     */
    private function verifyType($productId, $typeOperation, $quantity){
        
        $products = DB::table('products')->select('quantity')->where('id', '=', $productId)->get();
        $currentQuantity = $products[0]->quantity; 

        if($typeOperation == 'E'){
        
            $newQuantity = $this->sumValues($currentQuantity, $quantity);
            return $this->updateQuantity($productId, $newQuantity);    
        }

        $result = $currentQuantity > $quantity ? true : false;

        if($result){
            $newQuantity = $this->subtractValues($currentQuantity, $quantity);
            return $this->updateQuantity($productId, $newQuantity);    
        }
        
        return [
            'status' => false,
            'message' => 'A quantidade que você quer retirar é maior ou igual a que a que existe no estoque!'
        ];

    }

    /**
     * Update the new quantity of product by quantity sent by form
     */
    private function updateQuantity($productId, $newQuantity){

        $result = DB::table('products')
            ->where('id', '=', $productId)
            ->update(['quantity' => $newQuantity]);

        if($result == 1){
            return [
                'status' => true,
                'message' => 'Operação feita com sucesso!'
            ];
        }
        
        return [
            'status' => false,
            'message' => 'Erro ao ralizar operação!'
        ];
    }

    /**
     * Decode id of user in base64
     */
    private function getUserId($id){
        $userId = base64_decode($id);
        return $userId;
    }

    /**
     * Sum the values using the quantity in database and the quantity sent by form
     */
    private function sumValues($oldQuantity, $newQuantity){

        $quantity = $oldQuantity + $newQuantity;
        return $quantity;
    }

    /**
     * Subtract the values using the quantity in database and the quantity sent of form
     */
    private function subtractValues($oldQuantity, $newQuantity){
        $quantity = $oldQuantity - $newQuantity;
        return $quantity;
    }

    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $movement = Movement::find($id);

        
        if(isset($movement)){

            return response()->json([
                'status' => true,
                'body' => $movement, 
            ], 200);
            
            
        }

        return response()->json([
            'status' => false,
            'message' => 'Movimentação não encontrada!'
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
        $movement = Movement::find($id);

        if(isset($movement)){

            $result = $movement->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Movimentação atualizado com sucesso!'
            ], 200);
            
            
        }

        return response()->json([
            'status' => false,
            'message' => 'Movimentação não encontrada!'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $moviment = DB::table('movements')->where('id', '=', $id)->get()->first();
        $product = DB::table('products')->where('id', '=', $moviment->product_id)->get()->first();
        if($moviment->type == 'E'){
            $oldQuantity = $this->subtractValues($product->quantity, $moviment->quantity);
            DB::table('products')->where('id', '=', $moviment->product_id)->update(['quantity' => $oldQuantity]);
        }else{
            $oldQuantity = $this->sumValues($product->quantity, $moviment->quantity);
            DB::table('products')->where('id', '=', $moviment->product_id)->update(['quantity' => $oldQuantity]);
        }
        if(Movement::destroy($id)){
            
            return response()->json([
                'status' => true,
                'messsage' => 'Movimentação deletada com sucesso!'
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Movimentação não encontrada!'
        ], 404);
    }
}
