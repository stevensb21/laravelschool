<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;

class CourseController extends Controller
{
    public function index() {
        $post = new Group();
        dd($post->get());
    }
}
