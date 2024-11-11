<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user can register successfully.
     *
     * This test will:
     * - Disable CSRF middleware for the request.
     * - Send a POST request to the '/register' route with user registration data.
     * - Check that the response status is 302 (redirect).
     * - Check that the response redirects to the '/dashboard' route.
     * - Assert that the user was created in the 'users' database table.
     * - Assert that the user is authenticated after registration.
     *
     * @return void
     */
    public function test_user_can_register(): void
    {
        // Disable CSRF middleware
        $this->withoutMiddleware();
    
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => 'on',
        ]);
    
        // Check for redirect after registration
        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    
        // Assert that the user was created in the database
        $this->assertDatabaseHas('users', [
            'email' => 'john@doe.com',
        ]);
    
        // Assert that the user is authenticated
        $this->assertAuthenticated();
    }

    public function test_user_can_login(): void
    {
        // Disable CSRF middleware
        $this->withoutMiddleware();
    
        // Create a user with a known password
        $user = \App\Models\User::factory()->create([
            'password' => bcrypt($password = 'password123'),
        ]);
    
        // Simulate a login request
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);
    
        // Check if the response is a redirect to the intended page after login
        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    
        // Assert that the user is authenticated
        $this->assertAuthenticatedAs($user);
    }
    
    
}
