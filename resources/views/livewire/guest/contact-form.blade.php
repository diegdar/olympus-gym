<div>
    {{-- Mensaje de alerta --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.1500ms
            x-init="setTimeout(() => show = false, 3000)"
            class="absolute left-2 top-25 w-full z-50 bg-green-100 
                    border border-green-400 text-green-700 px-4 py-3 rounded 
                    dark:bg-green-800 dark:border-green-700 dark:text-green-200 text-center" role="alert">
            {{ session('success') }}
        </div>
    @endif           
    {{-- Form --}}
    <form wire:submit.prevent="submit" class="space-y-4 mx-4">
        @csrf
        <div>
            <input
                id="name"
                name="name"
                type="text" 
                wire:model="name" 
                required 
                placeholder="Tu nombre" 
                autocomplete="name"
                {{ $isSubmitted ? 'disabled' : '' }}
                class="mt-8 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-2 focus:border-[#fec544] focus:ring focus:ring-blue-200 disabled:bg-black-100 disabled:cursor-not-allowed">
            @error('name') 
                <div class="mt-1 text-sm text-red-600">*{{ $message }}</div> 
            @enderror
        </div>
        <div>
            <input
                id="email" 
                name="email"
                type="email" 
                wire:model="email" 
                required 
                placeholder="Tu Email" 
                autocomplete="email"
                {{ $isSubmitted ? 'disabled' : '' }}
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-2 focus:border-[#fec544] focus:ring focus:ring-blue-200 disabled:bg-black-100 disabled:cursor-not-allowed">
            @error('email') 
                <div class="mt-1 text-sm text-red-600">*{{ $message }}</div> 
            @enderror
        </div>

        <div>
            <input 
                id="subject" 
                name="subject"
                type="text" 
                wire:model="subject" 
                required 
                placeholder="Asunto" 
                autocomplete="off"
                {{ $isSubmitted ? 'disabled' : '' }}
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-2 focus:border-[#fec544] focus:ring focus:ring-blue-200 disabled:bg-black-100 disabled:cursor-not-allowed">
            @error('subject') 
                <div class="mt-1 text-sm text-red-600">*{{ $message }}</div> 
            @enderror
        </div>

        <div>
        <textarea 
            id="message-area" 
            name="message"
            wire:model="message" 
            rows="3" 
            required 
            placeholder="Tu mensaje" 
            autocomplete="off"
            {{ $isSubmitted ? 'disabled' : '' }}
            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-2 focus:border-[#fec544] focus:ring focus:ring-blue-200 disabled:bg-black-100 disabled:cursor-not-allowed resize-y min-h-[150px] max-h-[300px] field-sizing-content"></textarea>
            @error('message') 
                <div class="mt-1 text-sm text-red-600">*{{ $message }}</div> 
            @enderror
        </div>

        <button type="submit" 
                {{ $isSubmitted ? 'disabled' : '' }} 
                aria-label="Enviar mensaje"
                class="w-full rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700 focus:ring focus:ring-blue-300 disabled:bg-gray-400 disabled:cursor-not-allowed">
            Enviar mensaje
        </button>
    </form>
    @vite('resources/js/messageTransition.js')    
</div>