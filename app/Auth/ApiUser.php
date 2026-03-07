<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class ApiUser implements Authenticatable
{
    private $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    // Required methods for Authenticatable
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->attributes['id'];
    }

    public function getAuthPassword()
    {
        return null; // Not using password since it's API-based
    }

    // New in Laravel 9+
    public function getAuthPasswordName()
    {
        return 'password'; // Even if not used, return the field name
    }

    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // Not using remember token
    }

    public function getRememberTokenName()
    {
        return '';
    }

    // Magic method to access attributes
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    // Magic method to check attribute existence
    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    // Optional: Add method to get all attributes
    public function getAttributes()
    {
        return $this->attributes;
    }
}
