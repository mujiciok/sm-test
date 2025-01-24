<?php

declare(strict_types=1);

namespace App\Services\DataForSeoApi;

use App\Services\DataForSeoClient\RestClient;
use App\Services\DataForSeoClient\RestClientException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

abstract class DataForSeoApiEndpoint implements DataForSeoApiEndpointInterface
{
    protected string $url;
    protected string $fixturePath;
    /** @var array<class-string>  */
    protected array $validators = [];
    protected array $data = [];

    public function __construct(
        protected RestClient $client,
        protected bool $useFixtures = true,
    ) {
        $this->useFixtures = config('data_for_seo.use_fixtures');
    }

    protected function getRequestData(array $postData): array
    {
        if ($this->useFixtures) {
            return json_decode(Storage::disk('local')->get($this->fixturePath), true);
        }

        try {
            $response = $this->client->post($this->url, $postData);

            return $response['res']['tasks'][0]['data'] ?? [];
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

    protected function validate(): void
    {
        foreach ($this->validators as $validator) {
            App::make($validator)->validate($this->data);
        }
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
}
