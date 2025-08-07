<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuestTemplateController extends Controller
{
    /**
     * Show template preview for guests (non-authenticated users).
     */
    public function preview(Template $template): Response
    {
        // Check if template exists and is active
        if (!$template->is_active) {
            abort(404, 'القالب غير متاح');
        }

        // Load template with category
        $template->load('category');

        // Add thumbnail URL if exists
        $template->thumbnail_url = $template->thumbnail ? asset('storage/' . $template->thumbnail) : null;

        return Inertia::render('Guest/TemplatePreview', [
            'template' => $template,
            'canLogin' => true,
            'canRegister' => true,
        ]);
    }
}
