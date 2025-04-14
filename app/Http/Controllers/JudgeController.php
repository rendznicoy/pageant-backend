<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreJudgeRequest;
use App\Http\Resources\JudgeResource;
use App\Models\Judge;

class JudgeController extends Controller
{
    public function index()
    {
        return JudgeResource::collection(Judge::with('user')->get());
    }

    public function store(StoreJudgeRequest $request)
    {
        $judge = Judge::create($request->validated());
        return new JudgeResource($judge->load('user'));
    }
}
