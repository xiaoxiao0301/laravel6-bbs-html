<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReplyRequest;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


	public function store(ReplyRequest $request, Reply $reply)
	{
	    // 解决过滤后为空的情况
	    $content = clean($request->input('content'));
	    if (empty($content)) {
	        return back()->with('danger', '提交的评论含有非法内容');
        }

	    $reply->content = $content;
	    $reply->user_id = Auth::id();
	    $reply->topic_id = $request->input('topic_id');
	    $reply->save();
		return redirect()->to($reply->topic->link())->with('success', '评论创建成功!');
	}


	public function destroy(Reply $reply)
	{
		$this->authorize('destroy', $reply);
		$reply->delete();

		return redirect()->to($reply->topic->link())->with('success', '评论删除成功!');
	}
}
