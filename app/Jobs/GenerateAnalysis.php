<?php

namespace App\Jobs;

use App\Classes\AzureChat;
use App\Models\Idea;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAnalysis implements ShouldQueue
{
    use Queueable;

    public function __construct(public Idea $idea) {}

    public function handle(): void
    {
        $azure = new AzureChat;

        $messages = $this->idea->meetings()
            ->orderBy('created_at')
            ->get()
            ->map(fn($idea) => [
                'role' => 'user',
                'content' => $idea->resume
            ])
            ->toArray();
        $messages[] = [
            'role' => 'system',
            'content' => "Eres un analista financiero experto en evaluar el Retorno de Inversión (ROI). A partir de la información proporcionada sobre ingresos, gastos y contexto empresarial, genera un informe detallado en formato Markdown.

El informe debe incluir:

Calidad de los Datos:

- Evalúa la calidad de la información proporcionada.
- Si hay datos faltantes, genera una puntuación del 0% al 100%.
- Explica qué información falta y por qué es importante para un análisis preciso.
- Proporciona recomendaciones para mejorar la precisión del análisis.

Tabla de Gastos:
- Desglosa los principales gastos.
- Si hay gastos de inversión agrega una tabla de gastos iniciales
- Si hay gastos anuales, agrega una tabla de gastos anuales.
- Indica si falta información relevante.

Tabla de Ingresos:
Lista todas las fuentes de ingreso con sus respectivos montos.
Escenarios de Retorno de Inversión:
- Escenario Óptimo (Mejor caso): Explica qué sucedería si todas las condiciones son favorables, incluyendo ingresos máximos y gastos mínimos.
- Escenario esperado: Explica que sucederia con las condiciones mencionados por el cliente
- Escenario Pesimista (Peor caso): Explica qué sucedería si los ingresos fueran los más bajos esperados y los gastos los más altos posibles.

Evaluación de Riesgos:
- Identifica riesgos potenciales.
- Evalúa su impacto y propone formas de mitigarlos.

Cálculo del ROI:
- Calcula el Retorno de Inversión (ROI) en ambos escenarios
- Determina en cuánto tiempo se recuperaría la inversión en el peor y mejor de los casos.

Explica claramente los resultados y su implicación para la toma de decisiones.
El informe debe estar estructurado en formato Markdown para facilitar su lectura y presentación."
        ];

        $response = $azure->callApi($messages);
        $response = $response->json('choices.0.message.content') ?? null;
        $this->idea->update(['analysis' => $response]);
    }
}
