<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => true,
            'body' => User::orderBy('name', 'asc')->get()
        ]);
    }

    public function searchItems(Request $request){
        $fields = $request->all();

        return response()->json([
            'status' => true,
            'body' => User::where([
                            ['name', 'like', '%'.$fields['name'].'%'],
                            ['rm', 'like', '%'.$fields['rm'].'%']
                        ])->orderBy('name', 'asc')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $fields = $request->validated();

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => $fields['password'],
            'role' => $fields['role'],
            'disabled' => false
        ]);

        return response()->json([
            'status' => true,
            'name'   => $user->name,
            'email'  => $user->email,
            'role' => $user->role,
            'user_id'   => base64_encode($user->id),
            'token' => $user->createToken('userLogged')->plainTextToken
        ], 201);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->get('user');
        $user['disabled'] = false;
        $user = User::create($user);
        
        if($request->get('image')){
            $image64 = $request->get('image');
            $imageType = explode('/', explode(';', $image64)[0])[1];
            $imageName = Str::random(16).".$imageType";
            file_put_contents($imageName, $image64);
    
            $image = ['name' => $imageName, 'user_id' => $user->user_id];
            $image = UserImage::create($image);
        }

        if($user){
            return response()->json([
                'status' => true,
                'message' => 'Funcionário registrado com sucesso',
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'Erro ao adicionar funcionário!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = User::find(base64_decode($id));

        $employee['image'] = DB::table('user_images')->where('user_id', $employee->id)->get()->first();

        if(isset($employee)){
            return response()->json([
                'status' => true,
                'body' => $employee
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Usuário não encontrado'
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
        $employee = User::find($id);
        
        $image64 = $request->get('image');
        $imageType = explode('/', explode(';', $image64)[0])[1];
        $imageName = Str::random(16).".$imageType";
        file_put_contents($imageName, $image64);
    
        $new_image = ['name' => $imageName, 'user_id' => $employee->id];

        $image = DB::table('user_image')->where('user_id', $id)->get()->first();

        if(isset($employee)){
            $result = $employee->update($request->get('user'));
            if($image){
                $new_image['updated_at'] = date('Y-m-d H:i:s');
                DB::table('user_image')->where('user_id', $id)->update($new_image);
            }else{
                $new_image['created_at'] = "";
                DB::table('user_image')->insert($image);
            }

            return response()->json([
                'status' => true,
                'message' => 'Funcionário atualizado com sucesso!'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Funcionário não encontrado!'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = User::find($id);

        if(isset($employee)){
            $result = $employee->update(['disabled' => true]);
            return response()->json([
                'status' => true,
                'messsage' => 'Usuário deletado com sucesso!'
            ], 201);
        }           

        return response()->json([
            'status' => false,
            'message' => 'Usuário não encontrado!'
        ], 404);
    }

    public function deleteAll(Request $request){

        $items  = $request->all();
        $status = true;

        for ($i=0; $i < count($items); $i++) { 
            
            $employee = DB::table('users')
                            ->where('id', $items[$i])
                            ->update(['disabled' => true]); 

            if(!$employee){
                $status  = false;
                break;
            }
        }

        if($status){
            return response()->json([
                'status'    => true, 
                'message'   => "usuários deletados com sucesso!"
            ], 200);     
        }

        return response()->json([
            'status'    => false, 
            'message'   => "Erro ao realizar operação!"
        ], 400);     
    
    }
}
