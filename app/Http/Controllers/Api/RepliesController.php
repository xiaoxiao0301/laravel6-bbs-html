<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ReplyRequest;
use App\Models\Reply;
use App\Models\Topic;
use App\Transformers\ReplyTransformer;

class RepliesController extends Controller
{
    public function store(ReplyRequest $request, Topic $topic, Reply $reply, ReplyTransformer $replyTransformer)
    {
        $reply->content =  $request->input('content');
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
}
