<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CaptchaRequest;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Mews\Captcha\Captcha;

class CaptchasController extends Controller
{
    public function store(CaptchaRequest $request, Captcha $captcha)
    {
        $key = 'captcha-'.Str::random(15);
        $phone = $request->phone;
        // 判断手机号是否已被注册
        $users = User::where('phone', $phone)->first();
        if ($users) {
            return $this->response->errorForbidden('手机号已绑定其他用户,请直接登录');
        }
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
