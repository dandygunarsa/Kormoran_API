<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jawaban;
use App\LikeJawaban;

class JawabanController extends Controller
{
    public $successStatus = 200;

    public function postComment(Request $request){
        $jawaban = new Jawaban();
        $jawaban->pertanyaan_id = $request->pertanyaan_id;
        $jawaban->user_id = $request->user_id;
        $jawaban->jawaban = $request->jawaban;
        $jawaban->save();

        $status["msg"] = "comment posted!";

        return response()->json($status,$this->successStatus);
    }

    public function showComment($id){
        $jawabans = Jawaban::where('pertanyaan_id',$id)->get();


          
        $jawaban['msg'] = "succes";

        foreach($jawabans as $jawab){
            $jawaban['jawabanList'][] = array(
                'id' => $jawab['id'],
                'user_name' => $jawab->user->user_name,
                'user_pict' => $jawab->user->pict,
                'comment' => $jawab->jawaban,
                'like'=> $jawab->countLike($jawab['id']),
                'created_at' => $jawab['created_at'],
                'user_id' => $jawab->user_id
                );
        }
        
        return response()->json($jawaban,$this->successStatus);
    }

    public function commentLike($id, Request $request){
        $likejawaban = new LikeJawaban();
        $likejawaban->jawaban_id = $id;
        $likejawaban->user_id = $request->user_id;
        $likejawaban->save();

        $status["msg"] = "liked!";
        $status["id"] = $id;

        return response()->json($status,$this->successStatus);

    }

  
}
