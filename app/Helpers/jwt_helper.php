<?php

use \Firebase\JWT\JWT;

/**
 *
 * @param int    
 * @param string 
 * @return string
 */
function create_jwt($userId, $email)
{
    $key = getenv('JWT_SECRET_KEY'); 
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600;  
    $payload = [
        'iss' => 'SIMS-WEBAPP',  
        'sub' => $userId,        
        'email' => $email,       
        'iat' => $issuedAt,      
        'exp' => $expirationTime, 
    ];

    return JWT::encode($payload, $key);
}

/**
 *
 * @param string $jwt
 * @return object|null
 */
function verify_jwt($jwt)
{
    $key = getenv('JWT_SECRET_KEY'); 
    try {
        return JWT::decode($jwt, $key, ['HS256']);
    } catch (Exception $e) {
        return null; 
    }
}