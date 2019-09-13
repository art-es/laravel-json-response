<?php

use Artes\JsonResponse\JsonResponse;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class JsonResponseTest extends TestCase
{
    public function testSuccess(): void
    {
        $response = (new JsonResponse)->success();

        $this->assertEqualStatusAndContent($response, Response::HTTP_OK, ['success' => true]);
    }

    public function testSuccessFalse400(): void
    {
        $response = (new JsonResponse)->success(false, Response::HTTP_BAD_REQUEST);

        $this->assertEqualStatusAndContent($response, Response::HTTP_BAD_REQUEST, ['success' => false]);
    }

    public function testFail(): void
    {
        $response = (new JsonResponse)->fail();

        $this->assertEqualStatusAndContent($response, Response::HTTP_BAD_REQUEST, ['success' => false]);

        self::assertEquals(
            $response->content(),
            json_encode(['success' => false], JSON_UNESCAPED_UNICODE)
        );

        self::assertEquals(
            $response->getStatusCode(),
            Response::HTTP_BAD_REQUEST
        );
    }

    public function testFail404(): void
    {
        $response = (new JsonResponse)->fail(Response::HTTP_NOT_FOUND);

        self::assertEquals(
            $response->content(),
            json_encode(['success' => false], JSON_UNESCAPED_UNICODE)
        );

        self::assertEquals(
            $response->getStatusCode(),
            Response::HTTP_NOT_FOUND
        );
    }

    public function testWith(): void
    {
        $response = (new JsonResponse)
                ->success()
                ->with(['data' => 'value']);
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
