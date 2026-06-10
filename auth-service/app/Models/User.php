<?php

class User
{
    public ?int $id_user;
    public string $uuid;
    public string $name;
    public string $email;
    public string $password;
    public string $role;   // 'user' | 'admin'
    public string $status; // 'active' | 'inactive'
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $verification_token;

    /**
     * User constructor.
     *
     * @param array $data  Associative array with keys matching table columns.
     */
    public function __construct(array $data = [])
    {
        $this->id_user    = isset($data['id_user']) ? (int)$data['id_user'] : null;
        $this->uuid       = $data['uuid'] ?? '';
        $this->name       = $data['name'] ?? '';
        $this->email      = $data['email'] ?? '';
        $this->password   = $data['password'] ?? '';
        $this->role       = $data['role'] ?? 'user';
        $this->status     = $data['status'] ?? 'active';
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        $this->verification_token = $data['verification_token'] ?? null;
    }
}