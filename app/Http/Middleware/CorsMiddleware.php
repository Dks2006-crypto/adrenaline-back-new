<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigin = 'https://adrenaline-frontend.vercel.app';

        $response = $next($request);

        // Обрабатываем CORS только для нашего фронтенда
        if ($request->header('Origin') === $allowedOrigin) {
            $response->header('Access-Control-Allow-Origin', $allowedOrigin);
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization');
            $response->header('Access-Control-Allow-Credentials', 'true');
        }

        // Обработка Preflight OPTIONS запроса (нужна, если вы не используете ручной способ из index.php)
        if ($request->isMethod('OPTIONS')) {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', $allowedOrigin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}