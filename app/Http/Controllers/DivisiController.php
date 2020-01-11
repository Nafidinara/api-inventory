<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Divisi;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class DivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $divisi = Divisi::all();

        if(count($divisi)<=0){
            return response()->json([
                'message'=>'Data tidak ditemukan',
                'status_code' => '0002',
            ],404);
        }

        $response = [
            'message' => 'List Semua Divisi',
            'status_code' => '0001',
            'data' => $divisi
        ];

        return response()->json($response,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required'
        ]);

        try{
            DB::beginTransaction();
            $name = $request->input('name');

            $divisi = new Divisi();
            $divisi->name = $name;

            $divisi->save();

            DB::commit();

            return response()->json([
                'message' => 'Data berhasil disimpan',
                'status_code' => '0001',
                'data' => $divisi
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
        try{
            $divisi = Divisi::findOrFail($id);

        return response()->json([
            'message' => 'Data berhasil ditemukan',
            'status_code' => '0001',
            'data' => $divisi
        ],200);

        }catch(ModelNotFoundException $e){
            return response()->json([
                'message' => 'Data gagal ditemukan',
            'status_code' => '0004',
            'data' => '',
            'error' => $e
            ],404);
        }
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
            'name' => 'required|unique:divisis'
        ]);
        $name = $request->input('name');
        $divisi = Divisi::findOrFail($id);
        $divisi->name = $name;
        $divisi->update();

        if(!$divisi->update()){
            return response()->json([
                'message' => 'Data gagal di update',
                'status_code' => '0005',
                'data' => ''
            ],400);
        }

        return response()->json([
            'message' => 'Data berhasil di update',
            'status_code' => '0001',
            'data' => $divisi
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
        $divisi = Divisi::findOrFail($id);

        if(!$divisi->delete()){
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
