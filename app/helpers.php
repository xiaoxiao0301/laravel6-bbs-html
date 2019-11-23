<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

/**
 * @param $category_id
 * @return string
 */
function category_nav_active($category_id)
{
//    return active_class(if_route('categories.show') && if_route_param('category', $category_id));
    return active_class(if_route('categories.show') && if_route_param('category', $category_id));
}

/**
 * @param $value
 * @param int $length
 * @return string
 */
function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r\|\n+/', ' ', $value));
    return Str::limit($excerpt, $length);
}
