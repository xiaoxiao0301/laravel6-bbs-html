<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function show(Category $category, Request $request, Topic $topic)
    {
        $topics = Topic::withOrder($request->order)->where('category_id', $category->id)->with(['category', 'user'])->paginate(20);
        return view('topics.index', compact('topics', 'category'));
    }
}
