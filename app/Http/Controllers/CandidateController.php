<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCandidateRequest;
use App\Http\Resources\CandidateResource;
use App\Models\Candidate;

class CandidateController extends Controller
{
    public function index()
    {
        return CandidateResource::collection(Candidate::all());
    }

    public function store(StoreCandidateRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = file_get_contents($request->file('photo')->getRealPath());
        }

        $candidate = Candidate::create($data);
        return new CandidateResource($candidate);
    }
}
