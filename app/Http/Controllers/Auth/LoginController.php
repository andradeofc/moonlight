<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Adicione esta linha para importar a facade Log


class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        Log::info('User authenticated', [
            'user_id' => $user->id
        ]);
        
        // Redireciona para o dashboard em vez de verificar o plano por enquanto
        return redirect()->intended($this->redirectPath());
        
        /* Código a ser implementado depois
        if (!$user->hasActivePlan()) {
            return redirect()->route('plans.index')
                ->with('warning', 'Você precisa escolher um plano para acessar o sistema.');
        }
        */
    }
}