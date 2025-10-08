<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Freelancer;

//use App\Models\IpAddress;
//use App\Models\UserAgent;
//use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class FreelancerController extends Controller
{
    public function index(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'location_id' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validation->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $filter_location = $request->has('location_id') ? $request->location_id : null;

        $freelancers = Freelancer::when(isset($filter_location), fn($query) => $query->where('freelancer_id', $filter_location))
            ->orderBy('id', 'desc')
            ->get()
            ->transform(function ($obj) {
                return [
                    'id' => $obj->id,
                    'location_id' => $obj->location_id,
                    'first_name' => $obj->first_name,
                    'last_name' => $obj->freelancer_id,
                    'avatar' => $obj->avatar,
                    'username' => $obj->username,
                    'rating' => $obj->rating,
                    'total_jobs' => $obj->total_jobs,
                    'total_spent' => $obj->total_spent,
                    'previous_freelancers' => $obj->previous_freelancers,
                ];
            });

        return response()->json([
            'status' => 1,
            'data' => $freelancers,

        ], Response::HTTP_OK);
    }
}


