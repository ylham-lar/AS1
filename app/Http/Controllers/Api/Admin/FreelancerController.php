<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Freelancer;
use App\Models\Location;
use App\Models\IpAddress;
use App\Models\UserAgent;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FreelancerController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'location_id' => ['nullable', 'integer', 'min:1'],
        ]);
        $filter_location = $request->has('location_id') ? $request->location_id : null;

        $freelencers = Freelancer::when($filter_location, function ($query, $filter_location) {
            return $query->where('location_id', $filter_location);
        })
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => 1,
            'data' => [
                'Freelancers' => $freelencers,
            ],
        ], Response::HTTP_OK);
    }
}
