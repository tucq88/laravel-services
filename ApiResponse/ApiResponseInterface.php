<?php namespace App\Libraries\ApiResponse;

interface ApiResponseInterface
{
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
    public function responseJson($message, $errors = [], $data = [], $meta = [], $code = 200);

    /**
     * Response to common success request
     *
     * @param $message
     * @param $data
     * @param $meta
     *
     * @return mixed
     */
    public function success($message, $data, $meta);

    /**
     * Response to common error request
     *
     * @param       $message
     * @param array $errors
     * @param int   $code
     *
     * @return mixed
     */
    public function error($message, $errors = [], $code = 400);

    /**
     * Response to invalidated request
     *
     * @param      $errors
     * @param bool $message
     *
     * @return mixed
     */
    public function errorValidation($errors, $message = false);

    /**
     * Response to errors that come from server
     *
     * @param     $message
     * @param int $code
     *
     * @return mixed
     */
    public function errorInternal($message, $code = 500);
}