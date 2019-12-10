<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CaptchaRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Mews\Captcha\Captcha;

class CaptchasController extends Controller
{
    public function store(CaptchaRequest $request, Captcha $captcha)
    {
        $key = 'captcha-'.Str::random(15);
        $phone = $request->phone;
        /**
         *  captchaInfo :
         *   array {
                'sensitive' => false,
                'key' => '', 绑定的数值
         *      'img' => "dats:image/png;....."  base64 编码过后的
         *  }
         */
        $captchaInfo = $captcha->create('flat', true);
        $expiredAt = now()->addMinutes(10);
        Cache::put($key, ['phone' => $phone, 'code' => $captchaInfo['key']], $expiredAt);

        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_image_content' => $captchaInfo['img']
        ];

        return $this->response->array($result)->setStatusCode(201);
    }
}
