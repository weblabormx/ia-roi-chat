<?php

namespace App\Livewire;

use App\Jobs\GenerateAnalysis;
use App\Models\Idea;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SeeIdea extends Component
{
    public $idea;

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
        if($idea->meetings()->where('is_finished', true)->count() == 0) {
            return redirect('ideas/'.$idea->id.'/live_meeting');
        }

        if(request()->filled('regenerate')) {
            GenerateAnalysis::dispatch($idea);
            return redirect('ideas/'.$idea->id);
        }
    }

    public function newMeeting()
    {
        $this->idea->meetings()->create([
            'is_finished' => false
        ]);
        return redirect('ideas/'.$this->idea->id.'/live_meeting');
    }

    public function removeData()
    {
        $this->idea->delete();
        return redirect('dashboard');
    }

    public function exportPdf()
    {
        $html = Str::markdown($this->idea->analysis); // Convierte Markdown a HTML
        $pdf = Pdf::loadView('empty', ['html' => $html]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "documento.pdf"
        );
    }


    public function render()
    {
        return view('livewire.see-idea');
    }
}
