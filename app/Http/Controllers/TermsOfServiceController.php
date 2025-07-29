<?php

namespace App\Http\Controllers;

use App\Models\TermsOfService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TermsOfServiceController extends Controller
{
    /**
     * Display the active terms of service.
     */
    public function show(): Response
    {
        $terms = TermsOfService::getActive();

        if (!$terms) {
            return Inertia::render('TermsOfService/NotFound');
        }

        return Inertia::render('TermsOfService/Show', [
            'terms' => $terms,
        ]);
    }
}
