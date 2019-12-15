<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ReplyRequest;
use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;
use App\Transformers\ReplyTransformer;

class RepliesController extends Controller
{
    public function index(Topic $topic, ReplyTransformer $replyTransformer)
    {
        $replies = $topic->replies()->paginate(20);
        return $this->response->paginator($replies, $replyTransformer);
    }


    public function store(ReplyRequest $request, Topic $topic, Reply $reply, ReplyTransformer $replyTransformer)
    {
        $reply->content = $request->input('content');
        $reply->topic_id = $topic->id;
        $reply->user_id = $this->user()->id;
        $reply->save();

        return $this->response->item($reply, $replyTransformer)->setStatusCode(201);
    }

    public function destroy(Topic $topic, Reply $reply)
    {
        /**
         * 1. 回复的作者
         * 2. 话题的作者
         * 3. 管理员
         */
        if ($reply->topic_id != $topic->id) {
            return $this->response->errorBadRequest();
        }

        $this->authorize('destroy', $reply);
        $reply->delete();

        return $this->response->noContent();
    }

    public function userIndex(User $user)
    {
        $replies = $user->replies()->paginate(20);
        return $this->response->paginator($replies, new ReplyTransformer());
    }

}
