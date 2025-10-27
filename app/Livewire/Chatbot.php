<?php

namespace App\Livewire;

use Livewire\Component;

class Chatbot extends Component
{
    public $user;

    public function mount()
    {
        $this->user = auth()->user();
    }

    public function render()
    {
        return view('livewire.chatbot', [
            'user' => $this->user,
            'currentView' => request()->route()->getName(), // Asegúrate de pasar la vista actual
        ]);
    }
}
