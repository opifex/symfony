<?php

declare(strict_types=1);

namespace Tests\Support;

use Codeception\Actor;
use Codeception\Util\HttpCode;
use Exception;
use RuntimeException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends Actor
{
    use _generated\FunctionalTesterActions;

    public function getSchemaPath(string $filename): string
    {
        return codecept_data_dir() . 'Schema/' . $filename;
    }

    public function getResponseContent(string $filename): string
    {
        return file_get_contents(filename: codecept_data_dir() . 'Response/' . $filename);
    }

    public function haveHttpHeaderApplicationJson(): void
    {
        $this->haveHttpHeader(name: 'Content-Type', value: 'application/json');
    }

    public function haveHttpHeaderAuthorizationAdmin(string $email, string $password): void
    {
        $this->sendPost(url: '/api/auth/signin', params: json_encode(['email' => $email, 'password' => $password]));
        $this->seeResponseCodeIs(code: HttpCode::OK);
        $this->seeResponseIsJson();

        try {
            $accessToken = current($this->grabDataFromResponseByJsonPath(jsonPath: '$access_token'));
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $this->haveHttpHeader(name: 'Authorization', value: 'Bearer ' . $accessToken);
    }

    public function haveCleanMockServer(): void
    {
        try {
            $client = HttpClient::create(['base_uri' => getenv(name: 'MOCK_SERVER_URL')]);
            $client->request('PUT', 'reset');
        } catch (TransportExceptionInterface $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function haveMockResponse(Request $request, Response $response): void
    {
        $mockServerUrl = getenv(name: 'MOCK_SERVER_URL');
        $requestPath = ltrim(str_replace($mockServerUrl, replace: '', subject: $request->getUri()), characters: '/');

        try {
            $client = HttpClient::create(['base_uri' => $mockServerUrl]);
            $client->request('PUT', 'expectation', [
                'json' => [
                    'httpRequest' => ['method' => $request->getMethod(), 'path' => '/' . $requestPath],
                    'httpResponse' => [
                        'statusCode' => $response->getStatusCode(),
                        'headers' => array_map(fn($value) => $value[0], $response->headers->allPreserveCase()),
                        'body' => $response->getContent(),
                    ],
                ],
            ]);
        } catch (TransportExceptionInterface $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
