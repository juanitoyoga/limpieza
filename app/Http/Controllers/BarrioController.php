<?php

namespace App\Http\Controllers;

use App\Models\Barrio;
use Illuminate\Http\Request;

class BarrioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barrios = Barrio::select('id', 'id_DMQ', 'nombre', 'polygon', 'sector', 'parroquia')
            ->orderBy('nombre')
            ->get();

        return response()->json($barrios);
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
    public function show(Barrio $barrio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barrio $barrio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barrio $barrio)
    {
        //
    }
}
