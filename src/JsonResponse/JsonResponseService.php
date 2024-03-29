<?php
declare(strict_types=1);

namespace Artes\JsonResponse;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse as BaseJsonResponse;
use function is_array;
use function is_object;

/**
 * Class JsonResponseService
 * Service for getting formatted JSON response.
 * @package App\Services
 */
class JsonResponseService extends BaseJsonResponse
{
    public const
        ERROR_TYPE_MESSAGE = 0,
        ERROR_TYPE_VALIDATION = 1,
        ERROR_TYPE_SYSTEM = 2;

    /**
     * Overridden property for set default value.
     *
     * @var int $statusCode
     */
    protected $statusCode = self::HTTP_OK;

    /**
     * Set success field to false.
     *
     * @param  int  $type
     * @param  array|string|mixed  $errorData
     * @param  int|null  $code
     * @return JsonResponseService
     */
    public function error(int $type = self::ERROR_TYPE_MESSAGE, $errorData = null, ?int $code = null): JsonResponseService
    {
        $code = $code ?? self::HTTP_BAD_REQUEST;
        $this->setStatusCode($code);

        $data = [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => self::$statusTexts[$code],
                'type' => $type,
            ],
            'data' => $errorData,
        ];

        $this->setData($data);
        return $this;
    }

    /**
     * Get only your data to response.
     *
     * @param  mixed  $data
     * @param  int|null  $code
     * @return JsonResponseService
     */
    public function only($data, ?int $code = null): JsonResponseService
    {
        if ($code !== null) {
            $this->setStatusCode($code);
        }

        $this->setData($data);

        return $this;
    }

    /**
     * Set success field to $value arg (default true).
     *
     * @param  bool  $value
     * @param  int|null  $code
     * @return JsonResponseService
     */
    public function success(bool $value = true, ?int $code = null): JsonResponseService
    {
        $this->setStatusCode($code ?? self::HTTP_OK);

        $currentData = $this->getDecodedData();
        $currentData['success'] = $value;

        $this->setData($currentData);

        return $this;
    }

    /**
     * Throw http response exception.
     *
     * @param  int|null  $code
     */
    public function throw(?int $code = null): void
    {
        if ($code !== null) {
            $this->setStatusCode($code);
        }

        throw new HttpResponseException($this);
    }

    /**
     * Get default response data with your data.
     *
     * @param  mixed  $data
     * @param  int|null  $code
     * @return JsonResponseService
     */
    public function with($data, ?int $code = null): JsonResponseService
    {
        if ($code !== null) {
            $this->setStatusCode($code);
        }

        $currentData = $this->getDecodedData();

        if (is_array($data) || is_object($data)) {
            $data = array_merge($currentData, (array) $data);
        } else {
            $currentData[] = $data;
            $data = $currentData;
        }

        $this->setData($data);

        return $this;
    }

    /**
     * Get decoded current data.
     *
     * @return mixed
     */
    protected function getDecodedData()
    {
        return json_decode($this->data, true);
    }
}
