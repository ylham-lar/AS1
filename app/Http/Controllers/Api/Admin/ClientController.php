<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
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

        $clients = Client::when($filter_location, function ($query, $filter_location) {
            return $query->where('location_id', $filter_location);
        })
            ->orderBy('id')
            ->get()
            ->transform(function ($obj) {
                return [
                    'id' => $obj->id,
                    'location_id' => $obj->location_id,
                    'first_name' => $obj->first_name,
                    'last_name' => $obj->last_name,
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
            'data' => $clients,

        ], Response::HTTP_OK);
    }
}
