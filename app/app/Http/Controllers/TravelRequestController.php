<?php

namespace App\Http\Controllers;

use App\Models\TravelRequest;
use Illuminate\Http\Request;

class TravelRequestController extends Controller{
    public function index(Request $request){
        $query = TravelRequest::where('user_id', auth()->id());

        $query->when($request->filled('status'), fn($q) => $q->where('status', $request->status));
        $query->when($request->filled('destino'), fn($q) => $q->where('destino', 'like', '%'.$request->destino.'%'));
        $query->when($request->filled('start_date'), fn($q) => $q->whereDate('data_ida', '>=', $request->start_date));
        $query->when($request->filled('end_date'), fn($q) => $q->whereDate('data_volta', '<=', $request->end_date));

        $perPage = $request->get('per_page', 10);
        $requests = $query->orderBy('data_ida')->paginate($perPage);

        return response()->json($requests);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TravelRequest $travelRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TravelRequest $travelRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TravelRequest $travelRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TravelRequest $travelRequest)
    {
        //
    }
}
