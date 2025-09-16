<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardControlller extends Controller
{
    private $status   = true;
    private $quantity = [];
    private $sum      = [];
    private $products = [];

    public function index(Request $request)
    {
        
        $from = request('from')
            ? Carbon::parse(request('from'))->format('Y-m-d H:i:s')
            : "2024-05-01 00:00:00";

        $until = request('until')
            ? Carbon::parse(request('until'))->format('Y-m-d H:i:s')
            : Carbon::now()->format('Y-m-d H:i:s');

        $type   = request('type') ?? "";
        $userId = request('user') ?? 1;


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
            ->select('categories.name as category_name', DB::raw('SUM(products.price) as price'))
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
                'products.price',
                DB::raw('SUM(movements.quantity) as quantity'))
            ->where('movements.type', 'like', '%'.$type.'%')
            ->when($userId, function($query, $userId){ // apenas adiciona o where se o id user não for nulo
                return $query->where('movements.user_id', '=', $userId);
            })
            ->where('movements.created_at', '>=', $from)
            ->where('movements.created_at', '<=', $until)
            ->groupBy('products.id','products.name')
            ->get();

        $this->products = $total;
    }

}
