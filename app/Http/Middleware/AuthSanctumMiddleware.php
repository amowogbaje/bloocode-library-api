<?php
namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthSanctumMiddleware
{
    protected $sanctumMiddleware;

    public function __construct()
    {
        $this->sanctumMiddleware = new EnsureFrontendRequestsAreStateful;
    }
    
    public function handle($request, Closure $next)
    {
        try {
            $this->sanctumMiddleware->handle($request, function ($request) use ($next) {
                return $next($request);
            });
        } catch (AuthenticationException $e) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation Error.', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Resource Not Found.'], 404);
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (Exception $e) {
            return response()->json(['message' => 'Server Error.'], 500);
        }
    }
}
