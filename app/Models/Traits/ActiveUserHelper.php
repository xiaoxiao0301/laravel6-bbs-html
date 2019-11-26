<?php

namespace App\Models\Traits;

use App\Models\Reply;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait ActiveUserHelper
{
    // 用于存放用户临时数据
    protected $users = [];

    // 配置信息
    protected $topic_weight = 4;  // 话题权重
    protected $reply_weight = 1;  // 回复权重
    protected $pass_days = 7; // 多少天内发表
    protected $user_number = 6; // 取结果集个数

    // 缓存配置
    protected $cache_key = 'larabbs_active_uers';
    protected $cache_expire_in_minutes = 65 * 60;  // 6.0 缓存单位是秒

    public function getActiveUsers()
    {
        return Cache::remember($this->cache_key, $this->cache_expire_in_minutes, function () {
            return $this->calculateActiveUsers();
        });
    }

    public function calculateAndCacheActiveUsers()
    {
        // 获取活跃用户列表
        $active_users = $this->calculateActiveUsers();
        // 缓存数据
        $this->cacheActiveUser($active_users);
    }

    private function calculateActiveUsers()
    {
        $this->calculateTopicScore();
        $this->calculateReplyScore();

        // 排序
        $users = Arr::sort($this->users, function ($user) {
           return $user['score'];
        });

        // 倒序,高分在前,第二个参数表示保持key
        $users = array_reverse($users, true);
        $users = array_slice($users, 0, $this->user_number, true);

        $active_uers = collect();
        foreach ($users as $user_id => $user) {
            $user = $this->find($user_id);
            // 用户存在
            if ($user) {
                $active_uers->push($user);
            }
        }

        return $active_uers;
    }

    private function calculateTopicScore()
    {
        $topic_users = Topic::query()->select(DB::raw('user_id, count(*) as topic_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')
            ->get();
        foreach ($topic_users as $topic_user) {
            $this->users[$topic_user->user_id]['score'] = $topic_user->topic_count * $this->topic_weight;
        }
    }

    private function calculateReplyScore()
    {
        // 从回复数据表里取出限定时间范围($pass_days)内,有发表过回复的用户
        // 并且同时取出用户此段时间内发布回复的数量
        $reply_users = Reply::query()->select(DB::raw('user_id, count(*) as reply_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')
            ->get();

        // 计算分数
        foreach ($reply_users as $reply_user) {
            $reply_score = $reply_user->reply_count * $this->reply_weight;
            if (isset($this->users[$reply_user->user_id])) {
                $this->users[$reply_user->user_id]['score'] += $reply_score;
            } else {
                $this->users[$reply_user->user_id]['score'] = $reply_score;
            }
        }
    }


    private function cacheActiveUser($active_users)
    {
        // 将数据写入缓存中
        Cache::put($this->cache_key, $active_users, $this->cache_expire_in_minutes);
    }
}
