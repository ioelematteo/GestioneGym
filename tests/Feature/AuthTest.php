<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBasics();
    }

    public function test_chi_non_e_loggato_va_al_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_login_e_logout(): void
    {
        $user = $this->athlete();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_utente_bloccato_non_entra(): void
    {
        $user = $this->athlete();
        $user->update(['is_active' => false]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_honeypot_blocca_i_bot(): void
    {
        $user = $this->athlete();

        $this->post('/login', ['email' => $user->email, 'password' => 'password', 'website' => 'http://spam'])
            ->assertSessionHasErrors('website');
        $this->assertGuest();
    }

    public function test_un_utente_normale_non_vede_l_admin(): void
    {
        $this->actingAs($this->athlete())->get('/admin/utenti')->assertForbidden();
        $this->actingAs($this->athlete('admin'))->get('/admin/utenti')->assertOk();
    }
}
