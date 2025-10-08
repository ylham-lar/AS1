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
        $validation = Validator::make($request->all(), [
            'freelancer_id' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validation->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $filter_freelancer = $request->has('freelancer_id') ? $request->freelancer_id : null;

        $profiles = Profile::when($filter_freelancer, function ($query, $filter_freelancer) {
            return $query->where('freelancer_id', $filter_freelancer);
        })
            ->orderBy('id')
            ->get()->transform(function ($obj) {
                return [
                    'id' => $obj->id,
                    'freelancer_id' => $obj->freelancer_id,
                    'title' => $obj->title,
                    'body' => $obj->body,
                ];
            });;

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

        $profile = new Profile();
        $profile->freelancer_id = $request->freelancer_id;
        $profile->title = $request->title;
        $profile->body = $request->body;
        $profile->save();

        return response()->json([
            'status' => 1,
            'data' => [
                'id' => $profile->id,
                'freelancer_id' => $profile->freelancer_id,
                'title' => $profile->title,
                'body' => $profile->body,
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

        $profile = Profile::findOrFail($id);
        $profile->freelancer_id = $request->freelancer_id;
        $profile->title = $request->title;
        $profile->body = $request->body;
        $profile->update();

        return response()->json([
            'status' => 1,
            'data' => [
                'id' => $profile->id,
                'freelancer_id' => $profile->freelancer_id,
                'title' => $profile->title,
                'body' => $profile->body,
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
