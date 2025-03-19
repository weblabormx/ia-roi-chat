<?php

namespace App\Observers;

use App\Classes\AzureChat;
use App\Models\Idea;

class IdeaObserver
{
    public function creating(Idea $idea)
    {
        $azure = new AzureChat;
        $idea->thread_id = $azure->createThread($idea);
        dd($idea);
        if(!$idea->thread_id) {
            return false;
        }
    }

    public function created(Idea $idea)
    {
        // Instrucciones iniciales como mensaje del sistema
        $instructions = "Vas a tomar el rol de Asesor/consultor para emprededores, pequeñas y medianas empresas. Aqui hablarás sobre el proyecto '{$idea->title}'. 
            Tu labor es preguntar los datos necesarios para poder evaluar los riesgos de inversion de una calculadora ROI. Haras una pregunta y esperaras respuesta del usuario 
            hasta terminar con la informacion necesaria. Esos datos los deberas guardar para despues generar una respuesta en tablas y graficas con riesgos minimos y riesgos 
            maximos. Si te preguntan de otra cosa que no sea tu rol e,viaras el mensaje de que nada mas estas calificado para ayudar en esta labor. No me muestres lo capturado en 
            pantalla por el usuario, al final comentame el resumen de lo guardado. Genera un JSON con la informacion capturada";
        $azure->sendMessage($idea, $instructions, 'system'); 
    }
}
