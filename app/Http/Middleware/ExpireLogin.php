<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ExpireLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $deadline = $request->session()->get('login_expires_at');

            if (
                ! is_numeric($deadline)
                || now()->getTimestamp() >= (int) $deadline
            ) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $request->session()->flash(
                    'error',
                    'ログインの有効期限が切れました。再度ログインしてください。'
                );

                if (
                    $request->hasHeader('X-Livewire')
                    || $request->expectsJson()
                ) {
                    return response()->json([
                        'message' => 'ログインの有効期限が切れました。',
                        'login_url' => route('login'),
                    ], $request->hasHeader('X-Livewire') ? 419 : 401)
                        ->header('Cache-Control', 'no-store, private');
                }

                return redirect()->route('login');
            }
        }

        $response = $next($request);

        if (Auth::check()) {
            $response->headers->set(
                'Cache-Control',
                'no-store, private'
            );
        }

        return $response;
    }
}