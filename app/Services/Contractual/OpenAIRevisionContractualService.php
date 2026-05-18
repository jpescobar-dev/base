<?php

namespace App\Services\Contractual;

use App\Models\RevisionContractual;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIRevisionContractualService
{
    public function analizar(RevisionContractual $revision): array
    {
        $prompt = app(PromptRevisionContractualBuilderService::class)->build($revision);

        Log::info('Tamaño prompt contractual', [
            'revision_id' => $revision->id,
            'chars' => mb_strlen($prompt),
        ]);

        $response = Http::withToken(config('services.openai.key'))
            ->baseUrl(config('services.openai.base_url'))
            ->withOptions([
                'verify' => false, // temporal en local
            ])
            ->connectTimeout(30)
            ->timeout(300)
            ->post('/responses', [
                'model' => config('services.openai.model'),
                'input' => $prompt,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Error al consultar OpenAI: ' . $response->body());
        }

        return $response->json();
    }
}
