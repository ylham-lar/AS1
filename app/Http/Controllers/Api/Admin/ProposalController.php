<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProposalController extends Controller
{
    public function index(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'work_id' => ['nullable', 'integer', 'min:1'],
            'freelancer_id' => ['nullable', 'integer', 'min:1'],
            'profile_id' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validation->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        $filter_work = $request->has('work_id') ? $request->work_id : null;
        $filter_freelancer = $request->has('freelancer_id') ? $request->freelancer_id : null;
        $filter_profile = $request->has('profile_id') ? $request->profile_id : null;

        $proposals = Proposal::when(isset($filter_freelancer), fn($query) => $query->where('freelancer_id', $filter_freelancer))
            ->when(isset($filter_work), fn($query) => $query->where('work_id', $filter_work))
            ->when(isset($filter_profile), fn($query) => $query->where('profile_id', $filter_profile))
            ->orderBy('id')
            ->get()
            ->transform(function ($obj) {
                return [
                    'id' => $obj->id,
                    'work_id' => $obj->work_id,
                    'freelancer_id' => $obj->freelancer_id,
                    'profile_id' => $obj->profile_id,
                    'cover_letter' => $obj->cover_letter,
                ];
            });

        return response()->json([
            'status' => 1,
            'data' => $proposals,
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'work_id' => ['required', 'integer', 'min:1'],
            'freelancer_id' => ['required', 'integer', 'min:1'],
            'profile_id' => ['required', 'integer', 'min:1'],
            'cover_letter' => ['required', 'string', 'min:1'],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validation->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $proposal = new Proposal();
        $proposal->work_id = $request->work_id;
        $proposal->freelancer_id = $request->freelancer_id;
        $proposal->profile_id = $request->profile_id;
        $proposal->cover_letter = $request->cover_letter;
        $proposal->save();


        return response()->json([
            'status' => 1,
            'data' => [
                'id' =>  $proposal->id,
                'work_id' =>  $proposal->work_id,
                'freelancer_id' => $proposal->freelancer_id,
                'profile_id' =>  $proposal->profile_id,
                'cover_letter' =>  $proposal->cover_letter,
            ],
            'message' => 'Proposal created.',
        ], Response::HTTP_OK);
    }


    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'work_id' => ['required', 'integer', 'min:1'],
            'freelancer_id' => ['required', 'integer', 'min:1'],
            'profile_id' => ['required', 'integer', 'min:1'],
            'cover_letter' => ['required', 'string', 'min:1'],
            'status' => ['required', 'integer', 'min:0', 'max:2'],
        ]);
        if ($validation->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validation->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $proposal = Proposal::findOrFail($id);
        $proposal->work_id = $request->work_id;
        $proposal->freelancer_id = $request->freelancer_id;
        $proposal->profile_id = $request->profile_id;
        $proposal->cover_letter = $request->cover_letter;
        $proposal->update();

        return response()->json([
            'status' => 1,
            'data' => [
                'id' => $proposal->id,
                'work_id' => $proposal->work_id,
                'freelancer_id' => $proposal->freelancer_id,
                'profile_id' => $proposal->profile_id,
                'cover_letter' => $proposal->cover_letter,
            ],
            'message' => 'Proposal updated.',
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $obj = Proposal::findOrFail($id);
        $obj->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Proposal deleted',
        ], Response::HTTP_OK);
    }
}
