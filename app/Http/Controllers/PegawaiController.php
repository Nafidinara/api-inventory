<?php

namespace App\Http\Controllers;

use App\Divisi;
use App\Pegawai;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PegawaiController extends Controller
{
 /*
 * Display a listing of the resource.
 *
 * @return \Illuminate\Http\Response
 */
    public function index()
    {
        $pegawai = Pegawai::all();

        if(count($pegawai)<=0){
            return response()->json([
                'message'=>'Data tidak ditemukan',
                'status_code' => '0002',
            ],404);
        }

        $response = [
            'message' => 'List Semua Pegawai',
            'status_code' => '0001',
            'data' => $pegawai
        ];

        return response()->json($response,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'divisi_id' => 'required'
        ]);



        try{
            DB::beginTransaction();
            $name = $request->input('name');
            $divisi_id = $request->input('divisi_id');

            $pegawai = new Pegawai([
                'name' => $name,
                'divisi_id' => $divisi_id,
            ]);

            $pegawai->save();

            $divisi_name = Divisi::where('divisi_id',$divisi_id)->first();
            $pegawai->divisi_name = $divisi_name->name;

            DB::commit();

            return response()->json([
                'message' => 'Data berhasil disimpan',
                'status_code' => '0001',
                'data' => $pegawai
            ]);

        } catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'message' => 'Data gagal disimpan',
                'status_code' => '0003',
                'Data' => '',
                'error' => $e
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {

            $pegawai = Pegawai::findOrFail($id);
            $jumlah = Pegawai::count('pegawai_id');

            if($id > $jumlah){
                return response()->json([
                    'message' => 'Data gagal ditemukan',
                    'status_code' => '0004',
                    'data' => '',
                ],404);
            }
        return response()->json([
            'message' => 'Data berhasil ditemukan',
            'status_code' => '0001',
            'data' => $pegawai
        ],200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name' => 'required'
        ]);
        $name = $request->input('name');
        $divisi_id = $request->input('divisi_id');
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->name = $name;
        $pegawai->divisi_id = $divisi_id;
        $pegawai->update();

        if(!$pegawai->update()){
            return response()->json([
                'message' => 'Data gagal di update',
                'status_code' => '0005',
                'data' => ''
            ],400);
        }

        return response()->json([
            'message' => 'Data berhasil di update',
            'status_code' => '0001',
            'data' => $pegawai
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        if(!$pegawai->delete()){
            return response()->json([
                'message' => 'Data gagal di hapus',
                'status_code' => '0006'
            ],400);
        }

        return response()->json([
            'message' => 'Data behasil di hapus',
            'status_code' => '0001'
        ],200);
    }
}
