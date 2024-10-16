<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name'      => 'required|string',
            'email'     => 'required|email:rfc,dns|unique:users,email',
            'password'  => 'required|confirmed'
        ];
    }

    public function messages(){
        return [
            'name.required' => 'O campo nome é obrigatório!',
            'name.string'   => 'O campo nome precisa ser apenas em formato texto!',
            'email.required' => 'O campo email é obrigatório!',
            'email.email'   => 'O formato de email é inválido!',
            'email.unique'  => 'Esse email já está cadastrado!',
            'password.required'      => 'O campo senha é obrigatório!',
            'password.confirmed' => 'As senhas não conferem!'
        ];
    }

}
