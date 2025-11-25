<?php
namespace App\Http\Controllers\Kegiatan;

use Illuminate\Http\Request;
use App\Models\BebrasChallenge;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ChallengeController extends Controller
{
    public function index()
    {
        return view('kegiatan.challenge.index');
    }

    public function create()
    {
        return view('kegiatan.challenge.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun'      => 'required|digits:4|integer',
            'title'      => 'required|string|max:255',
            'tagline'    => 'nullable|string',
            'gambar'     => 'nullable|image|max:2048',
            'table_json' => 'nullable',
            'content'    => 'required',
        ]);

        // dd($validated);

        DB::beginTransaction();

        try {
            $slug = \Str::slug($validated['title']);

            $path = null;
            if ($request->hasFile('gambar')) {
                $path = $request->file('gambar')->store('bebras_challenge', 'public');
            }

            $data = BebrasChallenge::create([
                'tahun'      => $validated['tahun'],
                'title'      => $validated['title'],
                'slug'       => $slug,
                'tagline'    => $validated['tagline'] ?? null,
                'gambar'     => $path,
                'table_json' => $request->table_json ? json_decode($request->table_json, true) : null,
                'content'    => $validated['content'],
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data Bebras Challenge berhasil ditambahkan!');
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error("Error Store Bebras Challenge: " . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }
}
