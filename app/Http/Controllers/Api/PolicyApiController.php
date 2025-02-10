<?php

namespace App\Http\Controllers\Api;

use App\Models\Policy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PolicyApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $policies = Policy::get();

        return response()->json(['policies' => $policies]);
    }


    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
