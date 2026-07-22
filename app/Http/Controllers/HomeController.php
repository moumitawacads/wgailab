<?php

namespace App\Http\Controllers;

use App\Services\TestimonialsService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $testimonialsService;

    public function __construct(TestimonialsService $testimonialsService)
    {
        $this->testimonialsService = $testimonialsService;
    }
    public function index()
    {
        $testimonials = [];
        $testimonialsResponse = $this->testimonialsService->getTestimonials();
        if (isset($testimonialsResponse['success'])) {
            $testimonials = $testimonialsResponse['data'];
        }

        return view('frontend.pages.home', compact('testimonials'));
    }
}
