<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subject;
use App\Models\ImageDoc;
use Validator;
use Hash;
use Auth;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user()->with('subject','imgDoc')->first();
            $token =  $user->createToken('MyApp')-> accessToken;
   
            return response()->json([
                'message' => 'Login successfully',
                'user' => $user,
                'token' => $token,
                'status' => true,
                'status_code' => 200,
            ],200);
        } 
        else{ 
            return response()->json([
                'errors' => 'Email or password is incorrect',
                'status' => false,
                'status_code' => 400,
            ],400);
        } 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:255|unique:users,phone',
            'password' => 'required|string|max:255',
            'subject' => 'required',
            'image' => 'required',
            'document' => 'required'
        ]);

        if($validator->passes())
        {
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            if(isset($request->subject) && count($request->subject) > 0)
            {
                foreach ($request->subject as $key => $value) {
                    $sub = new Subject;
                    $sub->user_id = $user->id;
                    $sub->sub_name = $value;
                    $sub->save();
                }
            }

            if($request->hasFile('image'))
            {
                $destination = 'images';
                foreach($request->file('image') as $key => $value)
                {
                    $image = "IMG".$key.'-'.time().'-'.$value->getClientOriginalName();
                    $extension = $value->getClientOriginalExtension();
                    $image = str_replace(' ','-',$image);
                    $value->storeAs($destination,$image);
                    $path = $value->storeAs($destination, $image , 'public');
                    $imagedoc = new ImageDoc;
                    $imagedoc->user_id = $user->id;
                    $imagedoc->file_name = '/storage/' . $path;
                    $imagedoc->file_type = 'image';
                    $imagedoc->file_ext = $extension;
                    $imagedoc->save();
                }
            }
            if($request->hasFile('document'))
            {
                $destination = 'documents';
                foreach($request->file('document') as $key => $value)
                {
                    $document = "IMG".$key.'-'.time().'-'.$value->getClientOriginalName();
                    $extension = $value->getClientOriginalExtension();
                    $document = str_replace(' ','-',$document);
                    $path = $value->storeAs($destination, $document , 'public');
                    $imagedoc = new ImageDoc;
                    $imagedoc->user_id = $user->id;
                    $imagedoc->file_name = $path;
                    $imagedoc->file_type = 'document';
                    $imagedoc->file_ext = $extension;
                    $imagedoc->save();
                }
            }
            return response()->json([
                'message' => 'Register successfully',
                'status' => true,
                'status_code' => 200,
            ],200);
        }
        else {
            return response()->json([
                'errors' => $validator->errors(),
                'status' => false,
                'status_code' => 400,
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
