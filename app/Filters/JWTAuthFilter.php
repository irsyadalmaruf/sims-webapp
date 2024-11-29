<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use \Firebase\JWT\JWT;

class JWTAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwt = $request->getHeader('Authorization');
        
        if (!$jwt) {
            return Services::response()->setStatusCode(401)->setJSON(['error' => 'Authorization token not found']);
        }

        $jwt = str_replace('Bearer ', '', $jwt);

        $decoded = verify_jwt($jwt);

        if ($decoded) {
            $request->user = $decoded;
            return;
        }

        return Services::response()->setStatusCode(401)->setJSON(['error' => 'Invalid token']);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {

    }
}