<?php

namespace App\Http\Controllers;

use App\Challenge;
use Illuminate\Http\Request;

use Auth;
use Validator;

class ChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $challenges = Challenge::all();
        return view('challenge.index')->with('challenges', $challenges);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       return view('challenge.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
        'name' => 'required|unique:challenges',
        'time_period' => 'required',
        'entry_fee' => 'required',
        'status' => 'required'
        ])->validate();

        $challenge = new Challenge;
        $challenge->name = $request->name;
        $challenge->time_period = $request->time_period;
        $challenge->entry_fee = $request->entry_fee;
        $challenge->status = $request->status;
        $challenge->save();

        request()->session()->flash('success', 'Challenge added successfully!');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Challenge  $challenge
     * @return \Illuminate\Http\Response
     */
    public function show(Challenge $challenge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Challenge  $challenge
     * @return \Illuminate\Http\Response
     */
    public function edit(Challenge $challenge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Challenge  $challenge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $challenge = Challenge::find($id);
        $challenge->status = $request->status; 
        $challenge->save();
        if($request->status==1){
          $msg = 'enabled';
        }else{
          $msg = 'disabled';
        }
         back()->with('success','Challenge has been '.$msg.' successfully.');
         return redirect('challenges');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Challenge  $challenge
     * @return \Illuminate\Http\Response
     */
    public function destroy(Challenge $Challenge)
    {
        //
    }
}
