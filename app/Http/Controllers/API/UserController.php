<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Jawaban;
use App\Pertanyaan;

class UserController extends Controller
{

    public $successStatus = 200;

    public function login(){
        if(Auth::attempt(['user_name' => request('user_name'), 'password' => request('password')])){
            $user = Auth::user();
            return response()->json([
                'success' => 'success',
                'id' => $user->id,
                'status' => 'true',
                'user_name' => $user->user_name,
                'name' => $user->name,
                'email' => $user->email,
                'pict' => $user->pict,
                'token' =>  $user->createToken('KormoranApp')->accessToken,
        ], $this->successStatus);
        }

        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'user_name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        

    

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all();

        $count = User::where(['user_name' => $input['user_name']])->count();
        if($count) {

            // $response['status'] = 401;
            // $response['message'] = "Username already exist";

            return response()->json([
                'status'=> 401,
                'message' => 'Username already exist'   
                ], 401);      
        }

        $count = User::where(['email' => $input['email']])->count();
        if($count) {

            // $response['status'] = 401;
            // $response['message'] = "Email already used";

            return response()->json([
                'status' => 401,
                'message' => "Email already used",
            ], 401);      
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['pict'] = 'not selected yet';
        $user = User::create($input);
        return response()->json([
            'status'=>'true',
           'success'=>'success',
           'id' => $user->id,
           'token' =>  $user->createToken('KormoranApp')->accessToken,
           'name' =>  $user->name,
           'user_name' =>  $user->user_name,
           'email' =>  $user->email,
    ], $this->successStatus);
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }

    public function editProfile(Request $request){
        

        $userName = $request->user_name;
        $name = $request->name;
        $files = $request->file('imageUpload');

    
        if(!empty($files)) {
            $folderName = 'user';
            $fileName = $userName.'_image';
            $fileExtension = $files->getClientOriginalExtension();
            $fileNameToStorage = $fileName.'_'.time().'.'.$fileExtension;
            $filePath = $files->storeAs('public/'.$folderName , $fileNameToStorage); 
    }
        $user = User::find($request->id);

        // Storage::disk('public')->delete("/user/".$user->pict);

        $user->user_name = $userName;
        $user->name = $name;
        $user->pict = $fileNameToStorage;
        $user->save();

        return response()->json([
            'status' => 'true',
            'msg'=> 'Update profile success',
            'name' => $name,
            'user_name' => $userName,
            'pict' => $fileNameToStorage
        ]);
}

public function getUserQuestionHistory($id){
    $pertanyaans = Pertanyaan::where('user_id', $id)->orderBy('id','DESC')->get();
    
    $pertanyaan['msg'] = "succes";

    foreach($pertanyaans as $pertanya){
        $pertanyaan['pertanyaanList'][] = array(
            'id' => $pertanya['id'],
            'user_id' => $pertanya->user_id,
            'user_pict' => $pertanya->user->pict,
            'user_name' => $pertanya->user->user_name,
            'kategori' => $pertanya->kategori->kategori,
            'pertanyaan' => $pertanya['pertanyaan'],
            'pict' => $pertanya['pict'],
            'edited' => $pertanya['edited'],
            'total_jawaban' => $pertanya->countJawaban($pertanya['id']),
            'created_at' => $pertanya['created_at']
            );
    }
    
    return response()->json($pertanyaan,$this->successStatus);


}

public function getUserAnswerHistory($id){
    $jawabans = Jawaban::where('user_id',$id)->orderBy('id','DESC')->get();
      
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

public function deleteAnswer($id){
    $jawaban = Jawaban::find($id);
    $jawaban->delete();

    return response()->json(["msg"=>"comment deleted"],$this->successStatus);
}

public function deleteQuestion($id){
    $pertanyaan = Pertanyaan::find($id);
    $pertanyaan->delete();

    return response()->json(["msg"=>"question deleted"],$this->successStatus);
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

public function countQuestionAndAnswer($id){
    $user = User::findOrFail($id);

    $countJawaban = $user->countJawaban($id);
    $countPertanyaan = $user->countPertanyaan($id);

    return response()->json(['answer'=> $countJawaban,
                             'question'=> $countPertanyaan], $this->successStatus);

}

}