<?php

namespace App\Http\Controllers\API;

use App\Pertanyaan; 
use App\Jawaban; 
// use App\LikeJawaban;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Carbon\Carbon;
use Exception;
use Intervention\Image\ImageManagerStatic as Image;

class PertanyaanController extends Controller
{
    public $successStatus = 200;

    
    public function postPertanyaan(Request $request)
    {
        $request->validate([
           'user_id' => 'required',
           'kategori_id' => 'required',
           'question' => 'required|string',
           'imageUpload' => 'image|nullable|max:5000',
        ]);
        $files = $request->file('imageUpload');

        try {
    
            if(!empty($files)) {
                $folderName = 'pertanyaan';
                $fileName = $folderName.'_image';
                $fileExtension = $files->getClientOriginalExtension();
                $fileNameToStorage = $fileName.'_'.time().'.'.$fileExtension;

                $image_resize = Image::make($files->getRealPath())->resize(500,500);
                
                $filePath = $image_resize->save(public_path('storage/pertanyaan/'.$fileNameToStorage)); 
                
            } else {
                $filePath = NULL;
            }

            // dd($path);
 
            
            DB::table('tb_pertanyaan')->insert([
                'user_id' => $request->user_id,
                'kategori_id' => $request->kategori_id,
                'pertanyaan' => $request->question,
                'pict' => $fileNameToStorage,
                'edited' => "0",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);


        } catch (Exception $e) {
            
            return response()->json([
                'msg' => $e->getMessage(),
            ], 401);

        }

            return response()->json([
                'msg' => 'Pertanyaan berhasil di Upload'
            ], $this->successStatus);


    }

    public function showTrendingPertanyaan(Request $request)
    {

    
        $pertanyaans = Pertanyaan::limit(3)->orderBy('clicked','DESC')->get();
        
        $pertanyaan['msg'] = "succes";

        foreach($pertanyaans as $pertanya){
            $pertanyaan['pertanyaanList'][] = array(
                'id' => $pertanya['id'],
                'user_id' => $pertanya['user_id'],
                'user_pict' => $pertanya->user->pict,
                'user_name' => $pertanya->user->user_name,
                'kategori' => $pertanya->kategori->kategori,
                'kategori_id' => $pertanya->kategori_id,
                'pertanyaan' => $pertanya['pertanyaan'],
                'pict' => $pertanya['pict'],
                'edited' => $pertanya['edited'],
                'clicked' => $pertanya['clicked'],
                'total_jawaban' => $pertanya->countJawaban($pertanya['id']),
                'created_at' => $pertanya['created_at']
                );
        }
        
        return response()->json($pertanyaan,$this->successStatus);
    }



    public function showLatestPertanyaan(Request $request)
    {
    
        $pertanyaans = Pertanyaan::orderBy('id','DESC')->limit(3)->get();
        
        $pertanyaan['msg'] = "succes";

        foreach($pertanyaans as $pertanya){
            $pertanyaan['pertanyaanList'][] = array(
                'id' => $pertanya['id'],
                'user_id' => $pertanya['user_id'],
                'user_pict' => $pertanya->user->pict,
                'user_name' => $pertanya->user->user_name,
                'kategori' => $pertanya->kategori->kategori,
                'kategori_id' => $pertanya->kategori_id,
                'pertanyaan' => $pertanya['pertanyaan'],
                'pict' => $pertanya['pict'],
                'edited' => $pertanya['edited'],
                'total_jawaban' => $pertanya->countJawaban($pertanya['id']),
                'created_at' => $pertanya['created_at']
                );
        }
        
        return response()->json($pertanyaan,$this->successStatus);
    }

    public function showDetailPertanyaan($id){
        $pertanyaan = Pertanyaan::findOrFail($id);
        return response()->json([
            'msg' => 'success',
            'id' => $pertanyaan['id'],
            'user_pict' => $pertanyaan->user->pict,
            'user_name' => $pertanyaan->user->user_name,
            'kategori' => $pertanyaan->kategori->kategori,
            'pertanyaan' => $pertanyaan['pertanyaan'],
            'pict' => $pertanyaan['pict'],
            'edited' => $pertanyaan['edited'],
            'created_at' => $pertanyaan['created_at'],
        ], $this->successStatus);
    
    }

    public function onClickIncrease($id){
        $pertanyaan = Pertanyaan::findOrFail($id);

        $pertanyaan->clicked += 1;
        $pertanyaan->save();
        
        return response()->json(['msg' => 'success'], $this->successStatus);
    }

    public function showPertanyaanPerKategori($id){
        $pertanyaans = Pertanyaan::where('kategori_id','=',$id)->orderBy('id','DESC')->get();

        $pertanyaan['msg'] = "succes";

        foreach($pertanyaans as $pertanya){
            $pertanyaan['pertanyaanList'][] = array(
                'id' => $pertanya['id'],
                'user_id' => $pertanya['user_id'],
                'user_pict' => $pertanya->user->pict,
                'user_name' => $pertanya->user->user_name,
                'kategori' => $pertanya->kategori->kategori,
                'kategori_id' => $pertanya->kategori_id,
                'pertanyaan' => $pertanya['pertanyaan'],
                'pict' => $pertanya['pict'],
                'edited' => $pertanya['edited'],
                'total_jawaban' => $pertanya->countJawaban($pertanya['id']),
                'created_at' => $pertanya['created_at']
                );
        }
        
        return response()->json($pertanyaan,$this->successStatus);
    }

    public function showEditQuestion($id){
        $pertanyaan = Pertanyaan::findOrFail($id);
        
        $pertanyaan['msg'] = "success";
        return response()->json($pertanyaan, $this->successStatus);
    }

    public function editQuestion($id, Request $request){
        $pertanyaan = Pertanyaan::findOrFail($id);

        $pertanyaan->kategori_id = $request->kategori_id;
        $pertanyaan->pertanyaan = $request->pertanyaan;
        $pertanyaan->edited = "1";
        $pertanyaan->save();

        return response()->json(['msg' => "Update Success"], $this->successStatus);


    }

}
