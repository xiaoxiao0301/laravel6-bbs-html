<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $captchaData = Cache::get($request->captcha_key);
        if (!$captchaData) {
            $this->response->error('图片验证码失效', 422);
        }

        if (!captcha_api_check($request->captcha_code, $captchaData['code'])) {
            Cache::forget($request->captcha_key);
            $this->response->errorUnauthorized('验证码错误');
        }

        $phone = $captchaData['phone'];

        if (!app()->environment('production')) {
            $code = '1234';
        } else {
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);
            try {
                $result = $easySms->send($phone, [
                    'template' => 'SMS_179611210',
                    'data' => [
                        'code' => $code
                    ]
                ]);
            } catch (NoGatewayAvailableException $exception) {
                $message = $exception->getException('aliyun')->getMessage();
                $this->response->errorInternal($message ?? '发送短信异常');
            }

        }


        $key = 'verificationCodes_'.Str::random(15);
        $experiAt = now()->addMinutes(10);
        // 缓存验证码 10分钟过期
//        Log::debug('手机号缓存的key是:'.$key);
        Cache::put($key, ['phone' => $phone, 'code' => $code], $experiAt);
        // 清除图片验证码缓存
        Cache::forget($request->captcha_key);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $experiAt->toDateTimeString()
        ])->setStatusCode(201);
    }
}
