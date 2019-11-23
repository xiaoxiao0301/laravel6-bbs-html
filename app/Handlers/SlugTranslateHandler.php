<?php


namespace App\Handlers;



use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler
{
    public function translate($text)
    {
        $http = new Client;

        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate';
        $appId = config('services.baidu_translate.appid');
        $key = config('services.baidu_translate.key');
        $salt = time();
        if (empty($appId) || empty($key)) {
            return $this->pinyin($text);
        }

        $sign = md5($appId . $text. $salt . $key);
        $query = http_build_query([
            'q' => $text,
            'from' => 'zh',
            'to' => 'en',
            'appid' => $appId,
            'salt' => $salt,
            'sing' => $sign,
        ]);
        $response = $http->get($api.$query);
        $result = json_decode($response->getBody(), true);

        if (isset($result['trans_result'][0]['dst'])) {
            return Str::slug($result['trans_result'][0]['dst']);
        } else {
            return $this->pinyin($text);
        }

    }

    public function pinyin($text)
    {
        return Str::slug(app(Pinyin::class)->permalink($text));
    }

}
