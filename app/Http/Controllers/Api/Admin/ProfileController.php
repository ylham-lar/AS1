<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{

    public function index(Request $request)
    {
        $request->validate([
            'freelancer_id' => ['nullable', 'integer', 'min:1'],
        ]);
        $filter_freelancer = $request->has('freelancer_id') ? $request->freelancer_id : null;

        $profiles = Profile::when($filter_freelancer, function ($query, $filter_freelancer) {
            return $query->where('freelancer_id', $filter_freelancer);
        })
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => 1,
            'data' => [
                'Profiles' => $profiles,
            ],
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'freelancer_id' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'min:1'],
            'body' => ['required', 'string', 'min:1'],
        ]);
        if ($validation->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validation->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $objects = Profile::create([
            'freelancer_id' => $request->freelancer_id,
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return response()->json([
            'status' => 1,
            'data' => [
                'id' => $objects->id,
                'freelancer_id' => $objects->freelancer_id,
                'title' => $objects->title,
                'body' => $objects->body,
            ],
            'message' => 'Profile created.',
        ], Response::HTTP_OK);
    }


    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'freelancer_id' => ['required', 'integer', 'min:1'],
            'title' => ['required', 'string', 'min:1'],
            'body' => ['required', 'string', 'min:3'],
        ]);
        if ($validation->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validation->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $objects = Profile::findOrFail($id);
        $objects->freelancer_id = $request->freelancer_id;
        $objects->title = $request->title;
        $objects->body = $request->body;
        $objects->update();

        return response()->json([
            'status' => 1,
            'data' => [
                'id' => $objects->id,
                'freelancer_id' => $objects->freelancer_id,
                'title' => $objects->title,
                'body' => $objects->body,
            ],
            'message' => 'Profile updated.',
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $obj = Profile::findOrFail($id);
        $obj->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Profile deleted',
        ], Response::HTTP_OK);
    }

}
