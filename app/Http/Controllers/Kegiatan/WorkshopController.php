<?php

namespace App\Http\Controllers\Kegiatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    public function index()
    {
        return view('kegiatan.workshop.index');
    }
}
