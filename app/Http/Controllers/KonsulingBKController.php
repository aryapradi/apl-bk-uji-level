<?php

namespace App\Http\Controllers;

use App\Models\KonsulingBK;
use App\Models\LayananBK;
use App\Models\PengajuanKonseling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KonsulingBKController extends Controller
{

    public function indexWalas(){
        $walas = auth()->user()->guru;

        $kelas = $walas->walas_kelas;

        $siswas = $kelas->siswas;

        $jadwal_konsuling_siswas = [];

        foreach($siswas as $siswa){
            foreach($siswa->konsulings as $konsuling){
                array_push($jadwal_konsuling_siswas, $konsuling);
            }
        }

        return view('ganti nanti', compact('jadwal_konsuling_siswas'));
    }

    public function indexSiswa(){
        $siswa = auth()->user()->siswa;

        $jadwal_konsulings = $siswa->konsulings;

        return view('ganti nanti', compact('jadwal_konsulings'));
    }

    public function penjadwalanBKGuru(){
        return view('ganti nanti'); // from create jadwal nya
    }

    public function simpanKonsuling(Request $request, $namaLayanan){
        $LayananBK = LayananBK::where('jenis_layanan', $namaLayanan)->get()->first();
        $validate = $request->validate([
            'siswas_id' => 'required',
            'tanggal_konseling' => 'required',
            'tempat_id' => 'required',
            'ket' => 'required',
        ]);
        
        $Konsuling = KonsulingBK::create([
            'Layanan_BK_id' => $LayananBK->id,
            'bk_id' => auth()->user()->profile_type == 'guru' ? auth()->user()->guru->id : auth()->user()->siswa->kelas->bk_id,
            'tanggal_konseling' => $request->tanggal_konseling,
            'waktu_konseling' => $request->waktu_konseling,
            'tempat_id' => $request->tempat_id,
            'ket' => auth()->user()->profile_type == 'guru' ? $request->ket : '',
        ]);

        if(auth()->user()->profile_type == 'guru'){
            $Konsuling->update([
                'status' => 'ACCEPTED'
            ]);
        }

        if(auth()->user()->profile_type == 'siswa'){
            $Konsuling->update([
                'status' => 'PENDING'
            ]);

            $Pengajuan = PengajuanKonseling::create([
                'siswa_id' => auth()->user()->siswa->id,
                'alasan' => $request->alasan
            ]);

            DB::table('siswa_konsuling')->insert([
                'siswa_id' => auth()->user()->siswa->id,
                'konsuling_b_k_id' => $Konsuling->id
            ]);
        }

        foreach($request->siswas_id as $siswa){
            DB::table('siswa_konsuling')->insert([
                'siswa_id' => $siswa,
                'konsuling_b_k_id' => $Konsuling->id
            ]);
        }

        if(auth()->user()->profile_type == 'guru'){
            return redirect('/bk/konseling');
        }else{
            return redirect('/jadwalkonseling');
        }
    }

    public function menerimaPengajuan($id){
        $Konsuling = KonsulingBK::find($id);

        $Konsuling->update([
            'status' => 'ACCEPTED'
        ]);

        return 'ganti nanti';
    }

    public function menundaPengajuan(Request $request, $id){

        $validate = $request->validate([
            'tanggal_konseling' => 'required',
            'waktu_konseling' => 'required',
            'tempat_id' => 'required',
            'ket' => 'required',
        ]);

        $Konsuling = KonsulingBK::find($id);

        $Konsuling->update([
            'tanggal_konseling' => $request->tanggal_konseling,
            'waktu_konseling' => $request->waktu_konseling,
            'tempat_id' => $request->tempat_id,
            'ket' => $request->ket,
            'status' => 'ADJUORNED',
        ]);

        return 'ganti nanti';
    }

    public function mencatatHasil(Request $request, $id){
        $validate = $request->validate([
            'hasil' => 'required'
        ]);

        $Konsuling = KonsulingBK::find($id);

        $Konsuling->update([
            'hasil' => $request->hasil
        ]);

        return 'ganti nanti';
    }
} 