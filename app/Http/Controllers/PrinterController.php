<?php

namespace App\Http\Controllers;
use App\Printer;
use App\File as AppFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class PrinterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $printer = DB::table('file_printer')
        ->join('files','file_printer.file_id','=','files.file_id')
        ->join('printers','file_printer.printer_id','=','printers.printer_id')
        ->select('printers.divisi','printers.type','printers.printer_id','files.file_id')
        ->get();

        if(count($printer)<=0){
            return response()->json([
                'message'=>'Data tidak ditemukan atau masih kosong',
                'status_code' => '0002',
            ],404);
        }

        $response = [
            'message' => 'List Semua Pegawai',
            'status_code' => '0001',
            'data' => $printer,
        ];

        return response()->json($response,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'divisi' => 'required',
            'type' => 'required',
            'file' =>'required|mimes:jpg,pdf,jpeg'
            
        ]);

        try{
            DB::beginTransaction();
            $divisi = $request->input('divisi');
            $type = $request->input('type');
            $file = $request->file('file');

            $printer = new Printer([
                'divisi' => $divisi,
                'type' => $type,
            ]);
            $printer->save();

            
            $extension = $file->getClientOriginalExtension();
            $path = $file->getFilename().'.'.$extension;
            $name = 'file'.'.'.$extension;

            $files = new AppFile();
            $files->type = $file->getClientMimeType();
            $files->path = $path;

            Storage::disk('public')->putFileAs('file', new File($file), $name);

            $files->save();
            
            DB::table('file_printer')->insert([
                'printer_id' => $printer->printer_id,
                'file_id' => $files->file_id,
            ]);

            $file = AppFile::where('file_id', '=', $files->file_id)->first();
            $printer->file_id = $file->file_id;
            
                DB::commit();
                

            return response()->json([
                'message' => 'Data berhasil disimpan',
                'status_code' => '0001',
                'data' => $printer,
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
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $printer = DB::table('file_printer')
        ->join('files','file_printer.file_id','=','files.file_id')
        ->join('printers','file_printer.printer_id','=','printers.printer_id')
        ->select('printers.divisi','printers.type','printers.printer_id','files.file_id')
        ->where('file_printer.printer_id','=',$id);

        try{
            $printer->get();
            return response()->json([
                'message' => 'Data berhasil ditemukan',
                'status_code' => '0001',
                'data' => $printer
            ],200);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'message' => 'Data gagal ditemukan',
                'status_code' => '0004',
                'data' => $printer
            ],200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'divisi' => 'required',
            'type' => 'required',
            'file' => 'required|mimes:jpeg,jpg,pdf'

        ]);
        $divisi = $request->input('divisi');
        $type = $request->input('type');
        $printer = Printer::findOrFail($id);
        $printer->divisi =$divisi;
        $printer->type =$type;

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->getFilename().'.'.$extension;

        $files = AppFile::findOrFail($request->input('file_id'));
        $files->type = $file->getClientMimeType();
        $files->path = $path;

        Storage::disk('public')
        ->putFileAs('file', $file, 'printer'.'.'.$extension);
        $files->update();


        if((!$printer->update()) && (!$files->update())){
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
                $printer,
                $files
            ]
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try{
            DB::beginTransaction();
            $printer = Printer::findOrFail($id);
            $printer->delete();
            $files = AppFile::findOrFail($request->input('file_id'));
            $files->delete();

        if((!$printer->delete()) && (!$files->delete())){
            DB::commit();
        }

        return response()->json([
            'message' => 'Data berhasil di hapus',
            'status_code' => '0001'
        ],200);

        } catch(\Exception $e){
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
            $printer = Printer::findOrFail($id);

             $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $path = $file->getFilename().'.'.$extension;
            $name = 'printer'.'.'.$extension;

            $files = new AppFile();
            $files->type = $file->getClientMimeType();
            $files->path = $path;

            Storage::disk('public')->putFileAs('file', new File($file), $name);

            $files->save();
            
            DB::table('file_printer')->insert([
                'printer_id' => $printer->printer_id,
                'file_id' => $files->file_id,
            ]);

            $printer->file_id = $files->file_id;

            if($files->save()){
                DB::commit();
            }

            return response()->json([
                'message' => 'Foto berhasil ditambah',
                'status_code' => '0001',
                'data' => $printer,
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
