<?php

namespace App\Services;

use App\Exceptions\RepositoryException;
use App\Exceptions\ServiceException;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

readonly class AuthService
{
    public function __construct(private UserRepository $userRepository) {}

    /**
     * Attempt to authenticate a user with the given credentials.
     *
     * @throws ServiceException
     */
    public function login(string $email, string $password): array
    {
        // Attempt to authenticate the user
        if (! $token = auth()->attempt(['email' => $email, 'password' => $password])) {
            throw new ServiceException('Invalid credentials');
        }

        // Log the user in
        Log::info('User logged in', ['email' => $email]);

        // Return the authenticated user
        return [
            'token' => $token,
            'user' => auth()->user(),
        ];
    }

    /**
     * Register a new user with the given credentials.
     *
     * @throws ServiceException
     */
    public function register(array $credentials): void
    {
        // Create the user data
        $userData = [
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
        ];

        try {
            // Check if the user already exists
            if ($this->userRepository->exists('email', $credentials['email'])) {
                throw new ServiceException('User already exists');
            }

            // Create the user
            /** @var User $user */
            $user = $this->userRepository->create($userData);

            // Attach the user role
            $user->assignRole('user');
        } catch (RepositoryException $exception) {
            throw new ServiceException('Failed to create user', 1, $exception);
        }
    }

    public function logout(): void
    {
        auth()->logout();
    }
}
