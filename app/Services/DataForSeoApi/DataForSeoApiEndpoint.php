<?php

declare(strict_types=1);

namespace App\Services\DataForSeoApi;

use App\Services\DataForSeoApi\Enums\ResponseStatusCodeEnum;
use App\Services\DataForSeoApi\Exceptions\FailedApiResponseException;
use App\Services\DataForSeoApi\Exceptions\FixtureMissingException;
use App\Services\DataForSeoClient\RestClient;
use App\Services\DataForSeoClient\RestClientException;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

abstract class DataForSeoApiEndpoint implements DataForSeoApiEndpointInterface
{
    protected string $url;
    protected string $responsePath; // @TODO response handler classes instead
    /** @var array<class-string>  */
    protected array $validators = [];
    protected array $data = [];
    protected string $fixturePath;

    /**
     * @throws FailedApiResponseException
     */
    abstract protected function processRequest(): array;

    public function __construct(
        protected RestClient $client,
    ) {
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setData(string $key, mixed $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getData(string $key): mixed
    {
        return $this->data[$key];
    }

    /**
     * @throws Exception
     * @throws FixtureMissingException
     * @throws FailedApiResponseException
     */
    public function request(): array
    {
        $this->init();

        if (config('data_for_seo.use_fixtures')) {
            if (!Storage::disk('local')->exists($this->fixturePath)) {
                throw new FixtureMissingException();
            }

            return json_decode(Storage::disk('local')->get($this->fixturePath), true);
        }

        $this->validate();

        return $this->processRequest();
    }

    /**
     * @throws FailedApiResponseException
     */
    protected function getRequestData(array $postData): array
    {
        try {
            $response = $this->client->post($this->url, $postData);
            $this->handleInvalidResponse($response);
            $data = data_get($response, $this->responsePath);

            if (!$data) {
                Log::channel('dfs')->critical('API request failed', [
                    'response' => $response,
                    'responsePath' => $this->responsePath,
                ]);
                throw new FailedApiResponseException();
            }

            return Arr::first($data);
        } catch (RestClientException $e) {
            Log::channel('dfs')->alert($e->getMessage(), [
                'http_code' => $e->getHttpCode(),
                'error_code' => $e->getCode(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            exit();
        }
    }

    /**
     * @throws Exception
     */
    private function init(): void
    {
        if (!isset($this->fixturePath)) {
            throw new Exception('Fixture path not set');
        };
        if (!isset($this->url)) {
            throw new Exception('API url not set');
        };
        if (!isset($this->responsePath)) {
            throw new Exception('Response path not set');
        };
    }

    private function validate(): void
    {
        foreach ($this->validators as $validator) {
            App::make($validator)->validate($this->data);
        }
    }

    /**
     * @TODO handle other types of responses
     * @throws FailedApiResponseException
     */
    private function handleInvalidResponse(mixed $response): void
    {
        if (!$this->isSuccessStatusCode($response['status_code'])) {
            $this->throwFailedApiResponseException($response);
        }

        foreach ($response['tasks'] as $taskData) {
            // @TODO handle partial success/failure responses
            if (!$this->isSuccessStatusCode($taskData['status_code'])) {
                $this->throwFailedApiResponseException($response);
            }
        }
    }

    private function isSuccessStatusCode(int $statusCode): bool
    {
        return in_array($statusCode, [
            ResponseStatusCodeEnum::OK->value,
            ResponseStatusCodeEnum::TASK_CREATED->value,
        ]);
    }

    /**
     * @param array $response
     * @return never
     * @throws FailedApiResponseException
     */
    private function throwFailedApiResponseException(array $response): never
    {
        Log::channel('dfs')->critical('API request failed', ['response' => $response]);
        throw new FailedApiResponseException();
    }
}
