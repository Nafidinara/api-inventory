<?php

namespace App\Http\Controllers;

use App\Divisi;
use App\Laptop;
use App\File as AppFile;
use App\Pegawai;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LaptopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $laptop = DB::table('file_laptop')
        ->join('files','file_laptop.file_id','=','files.file_id')
        ->join('laptops','file_laptop.laptop_id','=','laptops.laptop_id')
        ->select('laptops.*','files.file_id')
        ->get();

        if(count($laptop)<=0){
            return response()->json([
                'message'=>'Data tidak ditemukan atau masih kosong',
                'status_code' => '0002',
            ],404);
        }

        $response = [
            'message' => 'List Semua Laptop',
            'status_code' => '0001',
            'data' => $laptop,
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
            'type' => 'required',
            'file' =>'required|mimes:jpg,pdf,jpeg',
            'serial_number' => 'required',
            'inventaris_code' => 'required',
            'operating_system' => 'required',
            'pegawai_id' => 'required'

        ]);

        try{
            DB::beginTransaction();
            $type = $request->input('type');
            $file = $request->file('file');
            $serial_number = $request->input('serial_number');
            $inventaris_code = $request->input('inventaris_code');
            $operating_system = $request->input('operating_system');
            $pegawai_id = $request->input('pegawai_id');

            $pegawai_name = Pegawai::where('pegawai_id',$pegawai_id)->first();
            $divisi_name = Divisi::where('divisi_id',$pegawai_name->divisi_id)->first();

            $laptop = new Laptop([
                'PIC' => $pegawai_name->name,
                'divisi' => $divisi_name->name,
                'type' => $type,
                'file' => $file,
                'serial_number' => $serial_number,
                'inventaris_code' => $inventaris_code,
                'operating_system' => $operating_system,
                'pegawai_id' => $pegawai_id,
            ]);

            $laptop->save();




            $extension = $file->getClientOriginalExtension();
            $path = $file->getFilename().'.'.$extension;
            $name = 'laptop'.'.'.$extension;

            $files = new AppFile();
            $files->type = $file->getClientMimeType();
            $files->path = $path;

            Storage::disk('public')->putFileAs('file/laptop', new File($file), $name);

            $files->save();

            DB::table('file_laptop')->insert([
                'laptop_id' => $laptop->laptop_id,
                'file_id' => $files->file_id,
            ]);

            $file = AppFile::where('file_id', '=', $files->file_id)->first();
            $laptop->file_id = $file->file_id;


                DB::commit();



            return response()->json([
                'message' => 'Data berhasil disimpan',
                'status_code' => '0001',
                'data' => $laptop,
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
            $laptop = DB::table('file_laptop')
        ->join('files','file_laptop.file_id','=','files.file_id')
        ->join('laptops','file_laptop.laptop_id','=','laptops.laptop_id')
        ->select('laptops.*','files.file_id')
        ->where('file_laptop.laptop_id','=',$id)->get();

        $jumlah = Laptop::count('laptop_id');
         if($id > $jumlah ){
             return response()->json([
                 'message' => 'Data gagal ditemukan',
                 'status_code' => '0004',
                 'data' => '',
             ],404);
         }
            return response()->json([
                'message' => 'Data berhasil ditemukan',
                'status_code' => '0001',
                'data' => $laptop
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
            'type' => 'required',
            'file' =>'required|mimes:jpg,pdf,jpeg',
            'serial_number' => 'required',
            'inventaris_code' => 'required',
            'operating_system' => 'required',
            // 'pegawai_id' => 'required'
        ]);

        $type = $request->input('type');
            $file = $request->file('file');
            $serial_number = $request->input('serial_number');
            $inventaris_code = $request->input('inventaris_code');
            $operating_system = $request->input('operating_system');
            // $pegawai_id = $request->input('pegawai_id');


            // dd($pegawai_name,$divisi_name);


            $laptop = Laptop::findOrFail($id);
            $laptop->type = $type;
            $laptop->serial_number = $serial_number;
            $laptop->inventaris_code = $inventaris_code;
            $laptop->operating_system = $operating_system;

            $extension = $file->getClientOriginalExtension();
            $path = $file->getFilename().'.'.$extension;

            $files = AppFile::findOrFail($request->input('file_id'));
            $files->type = $file->getClientMimeType();
            $files->path = $path;

            Storage::disk('public')
            ->putFileAs('file/laptop', $file, 'laptop'.'.'.$extension);
            $files->update();

            // dd($laptop);

            if((!$laptop->update()) && (!$files->update())){
                return response()->json([
                    'message' => 'Data gagal di update',
                    'status_code' => '0005',
                    'data' => ''
                ],400);
            }

            return response()->json([
                'message' => 'Data berhasil di update',
                'status_code' => '0001',
                'data' => [
                    $laptop,
                    $files
                ]
            ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request,$id)
    {
        $laptopAll = Laptop::all();
        try{
            DB::beginTransaction();
            $laptop = Laptop::findOrFail($id);
            $laptop->delete();
            $files = AppFile::findOrFail($request->input('file_id'));
            $files->delete();

            if((!$laptop->delete()) && (!$files->delete())){
                DB::commit();
            }

            return response()->json([
                'message' => 'Data berhasil di hapus',
                'status_code' => '0001',
                'data' => $laptopAll
            ],200);

        } catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'message' => 'Data gagal di hapus',
                'status_code' => '0006',
                'error' => $e
            ],400);
        }
    }

    public function tambahFile(Request $request, $id){

        $this->validate($request,[
            'file' =>'required|mimes:jpg,pdf,jpeg'
        ]);

        try{
            DB::beginTransaction();
            $laptop = Laptop::findOrFail($id);

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $path = $file->getFilename().'.'.$extension;
            $name = 'laptop'.'.'.$extension;

            $files = new AppFile();
            $files->type = $file->getClientMimeType();
            $files->path = $path;

            Storage::disk('public')->putFileAs('file/laptop', new File($file), $name);

            $files->save();

            DB::table('file_laptop')->insert([
                'laptop_id' => $laptop->laptop_id,
                'file_id' => $files->file_id,
            ]);

            $laptop->file_id = $files->file_id;

            if($files->save()){
                DB::commit();
            }

            return response()->json([
                'message' => 'Foto berhasil ditambah',
                'status_code' => '0001',
                'data' => $laptop,
            ]);

        } catch(\Exception $e){
            return response()->json([
                'message' => 'Foto gagal ditambah',
                'status_code' => '0007',
                'data' => '',
                'error' => $e
            ]);
        }

    }
}
