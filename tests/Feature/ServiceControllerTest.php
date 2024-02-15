<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Support;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServiceControllerTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    public function testCreateService()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
        Ticket::factory()->create();
        $service = Service::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/postService', [
            'requester_name' => $service->requester_name,
            'client_id' => $service->client_id,
            'service_area' => $service->service_area,
            'support_id' => $service->support_id,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'requester_name',
                'client_id',
                'service_area',
                'support_id',
            ],
        ]);

        $this->assertDatabaseHas('services', [
            'requester_name' => $response->json('data.requester_name'),
            'client_id' => $response->json('data.client_id'),
            'service_area' => $response->json('data.service_area'),
            'support_id' => $response->json('data.support_id'),
        ]);
    }

    public function testUpdateService()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Support::factory()->create();
        Ticket::factory()->create();
        $service = Service::factory()->create();

        $clientIds = Ticket::pluck('id')->toArray();
        $supportIds = Support::pluck('id')->toArray();

        $response = $this->actingAs($user)->putJson('/api/putService', [
            'service_id' => $service->id,
            'requester_name' => $this->faker->name,
            'client_id' => $this->faker->randomElement($clientIds),
            'service_area' => $this->faker->word,
            'support_id' => $this->faker->randomElement($supportIds),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'requester_name',
                'client_id',
                'service_area',
                'support_id',
            ],
        ]);

        $this->assertDatabaseHas('services', [
            'requester_name' => $response->json('data.requester_name'),
            'client_id' => $response->json('data.client_id'),
            'service_area' => $response->json('data.service_area'),
            'support_id' => $response->json('data.support_id'),
        ]);
    }

    public function testFindAllServices()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Ticket::factory()->create();
        Service::factory()->create();

        $response = $this->actingAs($user)->get('/api/getServices');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'requester_name',
                    'client_id',
                    'service_area',
                    'support_id',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function testFindServiceById()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Ticket::factory()->create();
        $service = Service::factory()->create();

        $response = $this->actingAs($user)->get('/api/getService?id=' . $service->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'requester_name' => $service->requester_name,
            'client_id' => $service->client_id,
            'service_area' => $service->service_area,
            'support_id' => $service->support_id,
        ]);
    }

    public function testDeleteServiceById()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Ticket::factory()->create();
        $service = Service::factory()->create();

        $response = $this->actingAs($user)->delete('/api/deleteService?id=' . $service->id);

        $response->assertStatus(200);
        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }

    public function testRestoreServiceById()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Ticket::factory()->create();
        $service = Service::factory()->create();
        $id = $service->id;
        $service->delete();

        $response = $this->actingAs($user)->post('/api/restoreService', ['id' => $id]);

        $response->assertStatus(200);
        $restoredService = Service::withTrashed()->find($service->id);
        $this->assertNotNull($restoredService);
        $this->assertNull($restoredService->deleted_at);
    }

    public function testFindServiceBySupportId()
    {
        $supportUser = User::factory()->create(['role' => User::ROLE_SUPPORT]);
        $support = Support::factory()->create();
        Ticket::factory()->create();
        $services = Service::factory(3)->create(['support_id' => $support->id]);

        $response = $this->actingAs($supportUser)->get('/api/getServiceSupport?support_id=' . $support->id);

        $response->assertStatus(200);
        foreach ($services as $service) {
            $response->assertJsonFragment([
                'requester_name' => $service->requester_name,
                'client_id' => $service->client_id,
                'service_area' => $service->service_area,
                'support_id' => $service->support_id,
            ]);
        }
    }

    public function testFindServiceByTicketId()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $ticket = Ticket::factory()->create();
        $services = Service::factory(3)->create(['client_id' => $ticket->id]);

        $response = $this->actingAs($user)->get('/api/getServiceTicket?ticket_id=' . $ticket->id);

        $response->assertStatus(200);
        foreach ($services as $service) {
            $response->assertJsonFragment([
                'requester_name' => $service->requester_name,
                'client_id' => $service->client_id,
                'service_area' => $service->service_area,
                'support_id' => $service->support_id,
            ]);
        }
    }

    public function testAssociateService()
    {
        $supportUser = User::factory()->create(['role' => User::ROLE_SUPPORT]);
        Ticket::factory()->create();
        $support = Support::factory()->create(['user_id' => $supportUser->id, 'service_area' => "service"]);
        $service = Service::factory()->create(['support_id' => NULL, "service_area" => $support->service_area]);

        $response = $this->actingAs($supportUser)->put('/api/putAssociateService?id=' . $service->id);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'requester_name' => $service->requester_name,
            'client_id' => $service->client_id,
            'service_area' => $service->service_area,
            'support_id' => $support->id,
        ]);

        $this->assertEquals($support->id, $service->fresh()->support_id);
    }

    public function testGetServiceAreas()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $serviceAreas = ['Support', 'Maintenance', 'Consulting'];
        $ticket = Ticket::factory()->create();

        foreach ($serviceAreas as $area) {
            Service::factory()->create(['service_area' => $area, 'client_id' => $ticket->id]);
        }

        $response = $this->actingAs($user)->get('/api/getServicesAreas');

        $response->assertStatus(200);
        foreach ($serviceAreas as $area) {
            $response->assertJsonFragment(['service_area' => $area]);
        }
    }

    public function testGetServiceTypes()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $serviceTypes = ['TypeA', 'TypeB', 'TypeC'];
        $ticket = Ticket::factory()->create();

        foreach ($serviceTypes as $area) {
            Service::factory()->create(['service' => $area, 'client_id' => $ticket->id]);
        }

        $response = $this->actingAs($user)->get('/api/getServicesTypes');

        $response->assertStatus(200);
        foreach ($serviceTypes as $area) {
            $response->assertJsonFragment(['service' => $area]);
        }
    }

    public function testUnassociateServices()
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPPORT]);
        $support = Support::factory()->create();
        $ticket = Ticket::factory()->create();

        $serviceWithSupport = Service::factory()->create(['support_id' => $support->id, 'client_id' => $ticket->id]);
        $serviceWithoutSupport = Service::factory()->create(['support_id' => NULL, 'client_id' => $ticket->id]);

        $response = $this->actingAs($user)->get('/api/getUnassociateServices');
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $serviceWithoutSupport->id])
            ->assertJsonMissing(['id' => $serviceWithSupport->id]);
    }

    public function testCompleteService()
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPPORT]);
        $support = Support::factory()->create();
        $ticket = Ticket::factory()->create();
        $service = Service::factory()->create(['support_id' => $support->id, 'client_id' => $ticket->id]);

        $response = $this->actingAs($user)->put('/api/putcompleteService', [
            'id' => $service->id,
            'status' => true,
            'service' => 'Service completed successfully.'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $service->id,
                    'status' => true,
                    'service' => 'Service completed successfully.'
                ]
            ]);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'status' => true,
            'service' => 'Service completed successfully.'
        ]);
    }

    public function testIncompletedServices()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Ticket::factory()->create();
        Service::factory()->count(3)->create(['status' => false]);

        $response = $this->actingAs($user)->get('/api/getIncompletedServices');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
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
        ]);
    }

    public function testCompletedServices()
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Ticket::factory()->create();
        Service::factory()->count(3)->create(['status' => true]);

        $response = $this->actingAs($user)->get('/api/getCompletedServices');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
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
        ]);
    }
}
