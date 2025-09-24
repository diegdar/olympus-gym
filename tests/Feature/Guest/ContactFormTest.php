<?php

declare(strict_types=1);

namespace Tests\Feature\Guest;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Mail\MailContact;
use App\Livewire\Guest\ContactForm;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use \App\Models\User;
use Database\Seeders\RoleSeeder;
use Tests\Traits\TestHelper;
use Livewire\Features\SupportTesting\Testable;

class ContactFormTest extends TestCase
{
    use RefreshDatabase, TestHelper;

    private const CONTACT_ROUTE = 'contact';

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);            
    }    

    public function test_contact_page_is_accessible_for_guests(): void
    {
        $this->get(route(self::CONTACT_ROUTE))
            ->assertStatus(200)
            ->assertSee('Contacta con nosotros')
            ->assertSee('Enviar mensaje');
    }

    public function test_contact_page_is_accessible_for_authenticated_users(): void
    {
        $user = User::factory()->create()->assignRole('member');

        $this->actingAs($user)->get(self::CONTACT_ROUTE)
             ->assertStatus(200)
             ->assertSee('Contacta con nosotros')
             ->assertSee('Enviar mensaje');
    }

    public function test_contact_form_is_present_in_contact_page(): void
    {
        $this->get(route(self::CONTACT_ROUTE))
             ->assertStatus(200)
             ->assertSee('wire:submit.prevent="submit"', false)
             ->assertSee('name="name"', false)
             ->assertSee('name="email"', false)
             ->assertSee('name="subject"', false)
             ->assertSee('name="message"', false);
    }

    private function getValidPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'subject' => 'Consulta de prueba',
            'message' => 'Este es un mensaje de prueba.'
        ], $overrides);
    }

    private function getLivewireComponent(array $payload = []): Testable
    {
        $payload = $this->getValidPayload($payload);
        
        return Livewire::test(ContactForm::class)
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->set('subject', $payload['subject'])
            ->set('message', $payload['message']);
    }

    public function test_contact_form_sends_email_to_admin(): void
    {
        Mail::fake();
        $payload = $this->getValidPayload();
        $this->getLivewireComponent($payload)
            ->call('submit')
            ->assertSet('isSubmitted', true)
            ->assertSee('Mensaje enviado! Gracias por contactarnos. Te responderemos pronto.');

        Mail::assertSent(MailContact::class, function ($mail) use ($payload) {
            return $mail->contact['name'] === $payload['name']
                && $mail->contact['email'] === $payload['email']
                && $mail->contact['subject'] === $payload['subject']
                && $mail->contact['message'] === $payload['message'];
        });
    }

    #[DataProvider('invalidContactFormDataProvider')]
    public function test_contact_form_validation_rules($field, $value, $expectedError): void
    {
        $component = $this->getLivewireComponent([$field => $value]);
        $component->call('submit')
            ->assertHasErrors([$field => $expectedError]);
    }

    public static function invalidContactFormDataProvider(): array
    {
        return [
            // field, value, expectedError
            ['name', '', 'required'],
            ['name', 'abc', 'min'],
            ['email', '', 'required'],
            ['email', 'no-email', 'email'],
            ['subject', '', 'required'],
            ['subject', 'abc', 'min'],
            ['message', '', 'required'],
            ['message', 'abc', 'min'],
        ];
    }

    public function test_contact_page_shows_google_maps_address(): void
    {
        $response = $this->get(route(self::CONTACT_ROUTE));
        $response->assertStatus(200);
        $response->assertSee('https://www.google.com/maps', false);
        $response->assertSee('Calle Ejemplo 123, 08001 Barcelona, España');
    }
}
