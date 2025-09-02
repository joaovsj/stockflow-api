<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class DashboardControlller extends Controller
{
    private $status   = true;
    private $quantity = [];
    private $sum      = [];
    private $products = [];

    public function index(Request $request)
    {
        
        $from   = request('from') ?? "2024-05-01";
        $until  = request('until') ?? date("Y/m/d");
        $type   = request('type') ?? "";
        $userId = request('user') ?? null;

        try {
            $this->quantityByCategory($from, $until, $type, $userId);
            $this->sumByCategory($from, $until, $type, $userId);
            $this->getProducts($from, $until, $type, $userId);

        } catch (\Throwable $th) {
            $status = false;        
        }

        return response()->json([
            'status'    => $this->status,
            "quantity"  => $this->quantity,
            "sum"       => $this->sum,
            "products"  => $this->products
        ]);
    }

    public function quantityByCategory($from = "", $until = "", $type = "", $userId = null)
    {

        $total = DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('movements', 'products.id', '=', 'movements.product_id')
            ->select('categories.name as category_name', DB::raw('SUM(movements.quantity) as total_quantity'))
            ->where('movements.type', 'like', '%'.$type.'%')
            ->when($userId, function ($query, $userId) { // apenas adiciona o where se o id user não for nulo
                return $query->where('movements.user_id', '=', $userId);
            })
            ->where('movements.created_at', '>=', $from)
            ->where('movements.created_at', '<=', $until)
            ->groupBy('categories.name')
            ->get();


          
        $labels = [];
        $amount = [];

        foreach ($total as $key => $value) {
        
            array_push($labels, $value->category_name);
            array_push($amount, intval($value->total_quantity));
        }
    
        $this->quantity = [ 
            'labels' => $labels,
            'amount' => $amount
        ];
    }

    public function sumByCategory($from = "", $until = "", $type = "", $userId = null)
    {

        $total = DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('movements', 'products.id', '=', 'movements.product_id')
            ->select('categories.name as category_name', DB::raw('SUM(movements.price) as price'))
            ->where('movements.type', 'like', '%'.$type.'%')
            ->when($userId, function($query, $userId){ // apenas adiciona o where se o id user não for nulo
                return $query->where('movements.user_id', '=', $userId);
            })
            ->where('movements.created_at', '>=', $from)
            ->where('movements.created_at', '<=', $until)
            ->groupBy('categories.name')
            ->get();
        
        $labels = [];
        $sum    = [];

        foreach ($total as $key => $value) {
        
            array_push($labels, $value->category_name);
            array_push($sum,    round($value->price, 2));
        }


        $this->sum = [ 
            'labels' => $labels,
            'sum'    => $sum  
        ];
    }

    public function getProducts($from = "", $until = "", $type = "", $userId = null){

        $total = DB::table('products')
            ->join('movements', 'movements.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name', 
                DB::raw('SUM(movements.quantity) as quantity'), 
                DB::raw('SUM(movements.price) as price'))
            ->where('movements.type', 'like', '%'.$type.'%')
            ->when($userId, function($query, $userId){ // apenas adiciona o where se o id user não for nulo
                return $query->where('movements.user_id', '=', $userId);
            })
            ->where('movements.created_at', '>=', $from)
            ->where('movements.created_at', '<=', $until)
            ->groupBy('products.id','products.name')
            ->get();
        
        foreach ($total as $key => $value) {
            $value->price = round($value->price, 2);
        }

        $this->products = $total;
    }

}
