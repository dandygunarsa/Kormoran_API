<?php

namespace App\Http\Controllers\API;

use App\Kategori;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class KategoriController extends Controller
{
    public $successStatus = 200;

    public function addKategori(Request $request){
        $kategoriName = $request->kategori;
        $files = $request->file('imageUpload');

        if(!empty($files)) {
            $folderName = 'kategori';
            $fileName = $kategoriName.'_image';
            $fileExtension = $files->getClientOriginalExtension();
            $fileNameToStorage = $fileName.'_'.time().'.'.$fileExtension;
            $image_resize = Image::make($files->getRealPath())->resize(500,500);
            
            $filePath = $image_resize->save(public_path('storage/pertanyaan/'.$fileNameToStorage)); 
    }   
        $kategori = new Kategori;
        $kategori->kategori = $kategoriName;
        $kategori->pict = $fileNameToStorage;

        $kategori->save();

        return response()->json([
            'status' => 'true',
            'kategori' => $kategoriName,
            'pict' => $fileNameToStorage
        ]);
    } 

    
    public function getAllKategori(){
        $kategoris = Kategori::get();

        $kategori['msg'] = "succes";

        foreach($kategoris as $kat){
            $kategori['kategoriList'][] = array(
                'id' => $kat->id,
                'kategori' => $kat->kategori,
                'pict' => $kat->pict
                );
        }

        return response()->json($kategori, $this->successStatus);
    }

    public function editKategori(Request $request){
      
        $kategoriName = $request->kategori1;
        $files = $request->file('imageUpload');

    
        if(!empty($files)) {
            $folderName = 'kategori';
            $fileName = $kategoriName.'_image';
            $fileExtension = $files->getClientOriginalExtension();
            $fileNameToStorage = $fileName.'_'.time().'.'.$fileExtension;
            $filePath = $files->storeAs('public/'.$folderName , $fileNameToStorage); 
    }
        $kategori = Kategori::find($request->id);

        Storage::disk('public')->delete("/kategori/".$kategori->pict);

        $kategori->kategori = $kategoriName;
        $kategori->pict = $fileNameToStorage;
        $kategori->save();

        return response()->json([
            'status' => 'true',
            'msg'=> 'Update profile success',
            'name' => $kategoriName,
            'pict' => $fileNameToStorage
        ]);

    }
}
