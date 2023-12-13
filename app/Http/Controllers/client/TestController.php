<?php

namespace App\Http\Controllers\client;

use App\Models\Test;
use App\Models\Answer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function show(int $id)
    {
    	$test = Test::findOrFail($id);
    	$userAnswers = Answer::where('test_id', $id)
    		->get()
    		->sortBy('points');

    	return view('client.tests.show', [
    		'test' => $test,
    		'userAnswers' => $userAnswers,
    	]);
    }
}
