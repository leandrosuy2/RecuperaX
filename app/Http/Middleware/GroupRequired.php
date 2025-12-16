<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GroupRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int  $groupId
     */
    public function handle(Request $request, Closure $next, $groupId): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Mapear groupId para roles permitidos
        $allowedRoles = [];

        switch ($groupId) {
            case 1: // Admin
                $allowedRoles = ['admin'];
                break;
            case 2: // Gestor ou Admin
                $allowedRoles = ['admin', 'gestor'];
                break;
            case 3: // Consultor, Gestor ou Admin
                $allowedRoles = ['admin', 'gestor', 'consultor'];
                break;
            case 4: // Todos os roles
                $allowedRoles = ['admin', 'gestor', 'consultor', 'credor'];
                break;
            default:
                abort(403, 'Grupo não reconhecido.');
        }

        // Verificar se o usuário tem o role necessário
        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Acesso negado. Você não tem permissão para acessar esta funcionalidade.');
        }

        return $next($request);
    }
}
