<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Location;
use App\Models\Freelancer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{

    public function index(Request $request)
    {
        $request->validate([
            'location_id' => ['nullable', 'integer', 'min:1'],
        ]);
        $filter_location = $request->has('location_id') ? $request->location_id : null;

        $clients = Client::when($filter_location, function ($query, $filter_location) {
            return $query->where('location_id', $filter_location);
        })
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => 1,
            'data' => [
                'Clients' => $clients,
            ],
        ], Response::HTTP_OK);
    }
}
