<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;

class Settings extends Component
{
    public $settings = [];
    public $rules = [
        'settings.chat_prompt' => 'required|string',
        'settings.meeting_prompt' => 'required|string',
        'settings.analysis_prompt' => 'required|string',
    ];

    public function mount()
    {
        $this->settings = Setting::get()->mapWithKeys(function ($setting) {
            return [$setting->column => $setting->value];
        })->all();
    }

    public function save()
    {
        $this->validate();
        foreach ($this->settings as $column => $value) {
            Setting::updateOrCreate(
                ['column' => $column],
                ['value' => $value]
            );
        }
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
