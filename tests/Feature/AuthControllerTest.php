<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Support;
use App\Models\Ticket;
use Tymon\JWTAuth\Support\RefreshFlow;

class AuthControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    public function testSignInWithValidCredentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 4,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    public function testSignInWithInvalidCredentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure(['error']);
    }

    public function testSignUpWithValidData()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'role' => 4,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);
    }

    public function testSignUpWithInvalidData()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'role' => 5,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message']);
    }

    public function testLogout()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/logout');

        $response->assertStatus(302);
    }

    public function testFindAvailableSupportWithAvailableSupport()
    {
        User::factory()->create(['role' => User::ROLE_SUPPORT]);        
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($user)->json('GET', '/api/getAvailableSupport');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'email',
                'role',
                'created_at',
                'updated_at',
                'support' => [
                    '*' => [
                        'id',
                        'user_id',
                        'service_area',
                        'created_at',
                        'updated_at',
                        'ticket_services' => [
                            '*' => [
                                'id',
                                'requester_name',
                                'client_id',
                                'service_area',
                                'support_id',
                                'status',
                                'created_at',
                                'updated_at',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testFindAvailableSupportWithNoAvailableSupport()
    {      
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $supportUser = User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create(['user_id' => $supportUser->id]);
        Ticket::factory()->create();
        Service::factory()->create(['status' => false]);

        $response = $this->actingAs($user)->json('GET', '/api/getAvailableSupport');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'No available support analyst']);
    }

    public function testFindAllSupport()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->create(['role' => User::ROLE_SUPPORT]);

        $response = $this->actingAs($user)->json('GET', '/api/getSupportList');

        $response->assertStatus(200);
    }

}
