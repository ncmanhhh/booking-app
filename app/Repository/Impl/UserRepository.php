<?php

namespace App\Repository\Impl;

use App\Models\User;
use App\Repository\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    private string $tableUser = 'users';

    /**
     * @param string $id
     * @return User|null
     */
    public function getById(string $id): ?User
    {
        $user = DB::table($this->tableUser)->where('uuid', $id)->first();

        if (!$user) {
            return null;
        }

        return $this->mapToModel($user);
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function getByEmail(string $email): ?User
    {
        $user = DB::table($this->tableUser)->where('email', $email)->first();

        if (!$user) {
            return null;
        }

        return $this->mapToModel($user);
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function getByEmailAndActive(string $email): ?User
    {
        $user = DB::table($this->tableUser)
            ->where('email', $email)
            ->whereNotNull('email_verified_at')
            ->first();

        if (!$user) {
            return null;
        }

        return $this->mapToModel($user);
    }

    /**
     * @param array $data
     * @return User
     */
    public function store(array $data): User
    {
        // Generate UUID if not provided
        if (!isset($data['uuid'])) {
            $data['uuid'] = (string) Str::uuid();
        }

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Set timestamps
        $now = now();
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        // Insert the record
        DB::table($this->tableUser)->insert($data);

        // Retrieve the newly created user
        return $this->getById($data['uuid']);
    }

    /**
     * @param array $data
     * @param string $id
     * @return User
     */
    public function update(array $data, string $id): User
    {
//        // Check if user exists
//        $user = $this->getById($id);
//
//        if (!$user) {
//            throw new \Exception("User with ID {$id} not found");
//        }
//
//        // Hash password if provided
//        if (isset($data['password'])) {
//            $data['password'] = Hash::make($data['password']);
//        }
//
//        // Set updated_at timestamp
//        $data['updated_at'] = now();
//
//        // Update the record
//        DB::table($this->tableUser)->where('uuid', $id)->update($data);
//
//        // Retrieve the updated user
//        return $this->getById($id);
        return new User();
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        // Check if user exists
        $user = $this->getById($id);

        if (!$user) {
            return false;
        }

        // Delete the record
        $deleted = DB::table($this->tableUser)->where('uuid', $id)->delete();

        return $deleted > 0;
    }

    /**
     * Map a database record to a User model.
     *
     * @param object $record
     * @return User
     */
    private function mapToModel(object $record): User
    {
        $user = new User();

        // Map all properties from the record to the model
        foreach ((array) $record as $key => $value) {
            $user->setAttribute($key, $value);
        }

        // Mark the model as existing
        $user->exists = true;

        return $user;
    }

}

