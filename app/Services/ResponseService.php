<?php

namespace App\Services;

class ResponseService
{
    const OK = 200;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOW = 405;
    const INTERNAL_SERVER_ERROR = 500;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;

    /**
     * 回應碼
     * 
     * @var int
     */
    protected $code;

    /**
     * 回應資料
     * 
     * @var \Illuminate\Support\Collection|array|null
     */
    protected $data;

    /**
     * 錯誤資料
     * 
     * @var \Illuminate\Support\Collection|array|string|null
     */
    protected $errors;

    /**
     * 標頭
     * 
     * @var \Illuminate\Support\Collection|array|null
     */
    protected $headers;

    /**
     * 視圖名稱
     * 
     * @var string|null
     */
    protected $view;

    /**
     * 建構函式
     */
    public function __construct()
    {
        $this->code = null;
        $this->data = null;
        $this->errors = null;
        $this->headers = null;
        $this->view = null;
    }

    /**
     * 處理空值
     * 
     * @return void
     */
    protected function emptyValueProcessor()
    {
        $this->code = (is_null($this->code)) ? self::OK : $this->code;
        $this->data = (empty($this->data)) ? [] : $this->data;
        $this->errors = (empty($this->errors)) ? [] : $this->errors;
        $this->headers = (empty($this->headers)) ? [] : $this->headers;
    }

    /**
     * 設定回應碼
     * 
     * @param int $code 回應碼
     * @return $this
     */
    public function setCode(int $code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * 設定回應資料
     * 
     * @param \Illuminate\Support\Collection|array|null $data 回應資料
     * @return $this
     */
    public function setData($data)
    {
        if ($data instanceof \Illuminate\Support\Collection) {
            $data = $data->toArray();
        }

        $this->data = $data;

        return $this;
    }

    /**
     * 設定錯誤資料
     * 
     * @param \Illuminate\Support\Collection|string|array|null
     * @return $this
     */
    public function setError($errors)
    {
        if ($errors instanceof \Illuminate\Support\Collection) {
            $errors = $errors->toArray();
        }

        $this->errors = $errors;
        
        return $this;
    }

    /**
     * 設定標頭
     * 
     * @param \Illuminate\Support\Collection|array|null $headers 標頭
     * @return $this
     */
    public function setHeaders($headers)
    {
        if ($headers instanceof \Illuminate\Support\Collection) {
            $headers = $headers->toArray();
        }

        $this->headers = $headers;

        return $this;
    }

    /**
     * 設定視圖名稱
     * 
     * @param string $view
     * @return $this
     */
    public function setView(string $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * 返回 JSON 格式回應
     * 
     * @return \Illuminate\Http\JsonResponse JSON 回應
     */
    public function json()
    {
        $this->emptyValueProcessor();

        return response()->json([
            'errors' => $this->errors,
            'data' => $this->data,
        ], $this->code, $this->headers);
    }

    /**
     * 返回視圖回應，其中視圖資料請以 setData 設定
     * 
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory 視圖
     */
    public function view()
    {
        if (empty($this->data) || $this->data->isEmpty()) {
            return view($this->view);
        } else {
            return view($this->view, $this->data);
        }
    }
}