<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    public function index()
    {
        // código para exibir a seção
        return view('user.action.index');
    }

    public function update(Request $request)
    {
        // código para atualizar a ação
        return redirect()->back()->with('success', 'Atualizado com sucesso!');
    }
}
