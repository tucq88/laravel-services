<?php namespace App\Libraries\ApiResponse;

use Illuminate\Pagination\LengthAwarePaginator;
use Lang;

/**
 * Handle API response
 *
 * @package App\Libraries\ApiResponse
 */
class ApiResponse implements ApiResponseInterface
{
    protected $response;

    public function setResponse($message, $errors, $data, $meta)
    {
        $response = [
            'message' => $message,
        ];

        if (!empty($errors) && is_array($errors))
            $response['error'] = [
                'status'   => true,
                'messages' => $errors
            ];

        if (!empty($data))
            $response['data'] = $data;

        if (!empty($meta))
            $response['meta'] = $meta;

        if ($data instanceof LengthAwarePaginator && empty($meta)) {
            $response['data'] = $data->items();
            $response['meta'] = [
                'pagination' => [
                    'total'        => $data->count(),
                    'last_page'    => $data->lastPage(),
                    'per_page'     => $data->perPage(),
                    'current_page' => $data->currentPage()
                ]
            ];
        }

        $this->response = $response;

        return $this->response;
    }

    /**
     * Common response to all request
     *
     * @param       $message
     * @param array $errors
     * @param array $data
     * @param       $meta
     * @param int   $code
     *
     * @return mixed
     */
    public function responseJson($message, $errors = [], $data = [], $meta = [], $code = 200)
    {
        return response()->json($this->setResponse($message, $errors, $data, $meta), $code);
    }

    /**
     * Response to common success request
     *
     * @param $message
     * @param $data
     * @param $meta
     *
     * @return mixed
     */
    public function success($message, $data, $meta = [])
    {
        $response = [
            'message' => $message,
            'data' => $data
        ];

        if ($data instanceof LengthAwarePaginator) {
            $response['data'] = $data->items();
            $response['meta'] = [
                'pagination' => [
                    'total'        => $data->count(),
                    'last_page'    => $data->lastPage(),
                    'per_page'     => $data->perPage(),
                    'current_page' => $data->currentPage()
                ]
            ];
        }

        return response()->json($response, 200);
    }

    /**
     * Response to common error request
     *
     * @param       $message
     * @param array $errors
     * @param int   $code
     *
     * @return mixed
     */
    public function error($message, $errors = [], $code = 400)
    {
        return response()->json([
            'message' => $message,
            'error'   => [
                'status'   => true,
                'messages' => $errors
            ]
        ], $code);
    }

    /**
     * Response to invalidated request
     *
     * @param      $errors
     * @param bool $message
     *
     * @return mixed
     */
    public function errorValidation($errors, $message = false)
    {
        if (!$message)
            $message = Lang::get('response.validation.fail');

        return response()->json([
            'message' => $message,
            'error'   => [
                'status'   => true,
                'messages' => $errors
            ]
        ], 422);
    }

    /**
     * Response to errors that come from server
     *
     * @param     $message
     * @param int $code
     *
     * @return mixed
     */
    public function errorInternal($message, $code = 500)
    {
        return response()->json([
            'message' => $message,
            'error' => [
                'status' => true
            ]
        ], $code);
    }

}