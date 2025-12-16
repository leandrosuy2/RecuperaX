<?php

namespace App\Services;

use Illuminate\Contracts\Hashing\Hasher;

class DjangoPasswordHasher implements Hasher
{
    /**
     * Hash the given value.
     */
    public function info($hashedValue)
    {
        return [];
    }

    /**
     * Hash the given value.
     */
    public function make($value, array $options = [])
    {
        // Não usamos isso para criar novas senhas, apenas para verificar
        return $value;
    }

    /**
     * Check the given plain value against a hash.
     */
    public function check($value, $hashedValue, array $options = [])
    {
        // Se for uma senha Django (pbkdf2_sha256)
        if (strpos($hashedValue, 'pbkdf2_sha256$') === 0) {
            return $this->checkDjangoPassword($value, $hashedValue);
        }

        // Senão, usa o hasher padrão do Laravel
        return app('hash')->check($value, $hashedValue);
    }

    /**
     * Check if the given hash has been hashed with the given options.
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        // Se for senha Django, precisa rehash
        return strpos($hashedValue, 'pbkdf2_sha256$') === 0;
    }

    /**
     * Verifica senha Django pbkdf2_sha256
     */
    protected function checkDjangoPassword($password, $djangoHash)
    {
        // Formato: pbkdf2_sha256$iterations$salt$hash
        $parts = explode('$', $djangoHash);
        
        if (count($parts) !== 4 || $parts[0] !== 'pbkdf2_sha256') {
            return false;
        }

        $iterations = (int) $parts[1];
        $salt = $parts[2];
        $hash = $parts[3];

        // Gera o hash usando pbkdf2
        $computedHash = base64_encode(hash_pbkdf2('sha256', $password, $salt, $iterations, 32, true));

        // Compara usando hash_equals para evitar timing attacks
        return hash_equals($hash, $computedHash);
    }
}
