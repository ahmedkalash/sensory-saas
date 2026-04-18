<?php

namespace Tests\Feature;

use App\Enums\UserType;
use App\Models\User;
use App\Filament\Resources\Users\Pages\CreateUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_forces_user_type_to_customer_when_created_via_filament(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'test@example.com')->first();
        
        $this->assertNotNull($user);
        $this->assertEquals(UserType::Customer, $user->type);
    }
}
