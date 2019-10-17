<?php

use Artes\JsonResponse\JsonResponseService as JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use PHPUnit\Framework\TestCase;

class JsonResponseTest extends TestCase
{
    public function testSuccess(): void
    {
        $response = (new JsonResponse)->success();
        $this->assertEqualStatusAndContent($response, 200, [
            'success' => true,
        ]);
    }

    public function testSuccessFalse400(): void
    {
        $response = (new JsonResponse)->success(false, JsonResponse::HTTP_BAD_REQUEST);
        $this->assertEqualStatusAndContent($response, 400, [
            'success' => false,
        ]);
    }

    public function testError(): void
    {
        $response = (new JsonResponse)->error();
        $this->assertEqualStatusAndContent($response, 400, [
            'success' => false,
            'error' => [
                'code' => 400,
                'message' => 'Bad Request',
                'type' => 0,
            ],
            'data' => null,
        ]);
    }

    public function testErrorValidation(): void
    {
        $validationErrors = [
            'key' => ['key field is required']
        ];
        $response = (new JsonResponse)->error(
            JsonResponse::ERROR_TYPE_VALIDATION,
            $validationErrors
        );
        $this->assertEqualStatusAndContent($response, 400, [
            'success' => false,
            'error' => [
                'code' => 400,
                'message' => 'Bad Request',
                'type' => 1,
            ],
            'data' => $validationErrors,
        ]);
    }

    public function testWith(): void
    {
        $response = (new JsonResponse)
            ->success()
            ->with(['data' => 'value']);
        $this->assertEqualStatusAndContent($response, 200, [
            'success' => true,
            'data' => 'value',
        ]);
    }

    public function testOnly(): void
    {
        $response = (new JsonResponse)
            ->success()
            ->only(['data' => 'value']);
        $this->assertEqualStatusAndContent($response, 200, [
            'data' => 'value',
        ]);
    }

    public function testThrow(): void
    {
        try {
            (new JsonResponse)
                ->error(JsonResponse::ERROR_TYPE_SYSTEM, null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR)
                ->throw();
        } catch (HttpResponseException $e) {
            /** @var JsonResponse $response */
            $response = $e->getResponse();
            $this->assertEqualStatusAndContent($response, 500, [
                'success' => false,
                'error' => [
                    'code' => 500,
                    'message' => 'Internal Server Error',
                    'type' => 2,
                ],
                'data' => null,
            ]);
            return;
        }

        $this->fail('Response exception not trowed');
    }

    protected function assertEqualStatusAndContent(JsonResponse $response, string $status, $content): void
    {
        self::assertEquals(
            $response->getStatusCode(),
            $status
        );

        self::assertEquals(
            $response->content(),
            json_encode($content, JSON_UNESCAPED_UNICODE)
        );
    }
}
