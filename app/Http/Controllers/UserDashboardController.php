<?php
namespace App\Http\Controllers;

use App\Models\Berita\BeritaForm;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user        = auth()->user();
        $totalBerita = BeritaForm::where('user_id', $user->id)->count();
        $draft       = BeritaForm::where('user_id', $user->id)->where('status', 'draft')->count();
        $submitted   = BeritaForm::where('user_id', $user->id)->where('status', 'submitted')->count();
        $approved    = BeritaForm::where('user_id', $user->id)->where('status', 'approved')->count();
        $published   = BeritaForm::where('user_id', $user->id)->where('status', 'published')->count();
        $rejected    = BeritaForm::where('user_id', $user->id)->where('status', 'rejected')->count();

        return view('user.dashboard', compact(
            'user',
            'totalBerita',
            'draft',
            'submitted',
            'approved',
            'published',
            'rejected'
        ));
    }
}
