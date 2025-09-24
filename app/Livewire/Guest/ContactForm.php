<?php

declare(strict_types=1);

namespace App\Livewire\Guest;

use Livewire\Component;
use App\Mail\MailContact;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Mail;

class ContactForm extends Component
{
    public bool $isSubmitted = false;

    #[Rule('required|min:5|max:255')]
    public string $name = '';
    #[Rule('required|email')]
    public string $email = '';
    #[Rule('required|string|min:5|max:255')]
    public string $subject = '';
    #[Rule('required|string|min:5|max:500')]
    public string $message = '';

    protected array $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.min' => 'El nombre debe tener al menos 5 caracteres.',
        'name.max' => 'El nombre no puede tener más de 255 caracteres.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'Debe ingresar un correo electrónico válido.',
        'subject.required' => 'El asunto es obligatorio.',
        'subject.string' => 'El asunto debe ser un texto válido.',
        'subject.min' => 'El asunto debe tener al menos 5 caracteres.',
        'subject.max' => 'El asunto no puede tener más de 255 caracteres.',
        'message.required' => 'El mensaje es obligatorio.',
        'message.string' => 'El mensaje debe ser un texto válido.',
        'message.min' => 'El mensaje debe tener al menos 5 caracteres.',
        'message.max' => 'El mensaje no puede tener más de 500 caracteres.',
    ];

    /**
     * Handle the contact form submission.
     */
    public function submit(): void
    {
        $this->validate();

        Mail::to(config('mail.from.address'))->send(new MailContact([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ]));

        session()->flash('success', 'Mensaje enviado! Gracias por contactarnos. Te responderemos pronto.');
        $this->isSubmitted = true;
        $this->dispatch('formSubmitted');
        $this->reset(['name', 'email', 'subject', 'message']);
    }

    /**
     * Render the contact form view.
     */
    public function render()
    {
        return view('livewire.guest.contact-form');
    }
}