<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AdmisionApiService
{
    protected string $baseUrl;
    protected ?string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.admision.url'), '/');
        $this->token = config('services.admision.token');
    }

    protected function request(bool $auth = true): PendingRequest
    {
        $request = Http::acceptJson()->timeout(45)->retry(2, 500);

        if ($auth) {
            if (!$this->token) {
                throw new RuntimeException('No se configuró ADMISION_API_TOKEN en el archivo .env.');
            }

            $request = $request->withToken($this->token);
        }

        return $request;
    }

    public function procesos(): array
    {
        return $this->request(false)
            ->get($this->baseUrl . '/api/get-procesos')
            ->throw()
            ->json();
    }

    public function programas(): array
    {
        return $this->request()
            ->get($this->baseUrl . '/api/v1/select-programas-admision')
            ->throw()
            ->json();
    }

    public function postulantes(int $proceso, int $programa): array
    {
        return $this->request()
            ->get($this->baseUrl . "/api/v1/get-postulantes-proceso/{$proceso}/{$programa}")
            ->throw()
            ->json();
    }
}
