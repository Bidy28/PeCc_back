<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    //
    /**
     * Retourne la liste des services avec leurs prix.
     */
    // public function index()
    // {
    //     return response()->json(
    //         Service::query()
    //             ->select(['id', 'nom', 'prix', 'elements'])
    //             ->orderBy('nom')
    //             ->get()
    //     );
    // }

    public function index()
    {
        $services = Service::query()
            ->select(['id', 'nom', 'prix', 'elements'])
            ->orderBy('nom')
            ->get();
        return response()->json($services);
    }
}
