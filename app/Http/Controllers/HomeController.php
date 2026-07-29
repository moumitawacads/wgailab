<?php

namespace App\Http\Controllers;

use App\Services\ContentsService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $contentsService;

    public function __construct(ContentsService $contentsService)
    {
        $this->contentsService = $contentsService;
    }
    public function index()
    {
        $contents = [];
        $contentsResponse = $this->contentsService->getContents();
        if (isset($contentsResponse['success'])) {
            $contents = $contentsResponse['data'];
        }

        return view('frontend.pages.home', compact('contents'));
    }
}
