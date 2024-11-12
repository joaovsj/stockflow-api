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
            ->select('m.*', 'p.name as product', 'u.name as user', 'c.name as category', 'un.name as unity')
            ->orderBy('created_at', 'desc')
            ->crossJoin('users as u', 'u.id', '=', 'user_id')
            ->crossJoin('products as p', 'p.id', '=', 'product_id')
            ->crossJoin('units as un', 'un.id', '=', 'p.unity_id')
            ->crossJoin('categories as c', 'c.id', '=', 'p.category_id')
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
        $movement = $request->all();
        $userId = $request['user_id'];

        $productId      = $request['product_id'];
        $typeOperation  = $request['type'];
        $quantity       = $request['quantity'];
        
        $request['created_at'] = str_replace('T', ' ', $request['created_at']);

        $result = $this->verifyType($productId, $typeOperation, $quantity);
        
        if($result['status'] == false){

            return response()->json([
                'status' => false,
                'message' => $result['message']
            ]);

        }

        //$request['user_id'] = base64_decode($request['user_id']);

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
        
        if($typeOperation == 'E'){
            DB::table('products')->where('id', '=', $productId)->increment('quantity', $quantity);
            return [
                'status' => true,
                'message' => 'Operação feita com sucesso!'
            ];
        }
        
        $products = DB::table('products')->select('quantity')->where('id', '=', $productId)->get()->first();
        $currentQuantity = $products->quantity; 
        
        $result = $currentQuantity > $quantity ? true : false;
        
        if($result){
            DB::table('products')->where('id', '=', $productId)->decrement('quantity', $quantity);
            return [
                'status' => true,
                'message' => 'Operação feita com sucesso!'
            ];
        }
        
        return [
            'status' => false,
            'message' => 'A quantidade que você quer retirar é maior do que existe no estoque!'
        ];

    }

    /**
     * Decode id of user in base64
     */
    private function getUserId($id){
        $userId = base64_decode($id);
        return $userId;
    }

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
