<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\Assessor;
use App\Course;
use App\Slo;
use App\Team;
use App\Goal;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams = Team::get();
        $initialTeams = Team::whereNull('final')->orWhere('final', '=', 0)->orderBy('name')->get();
        $finalTeams = Team::where('final', '=', 1)->orderBy('name')->get();
        return view('assessment.index')->with(compact('teams','initialTeams','finalTeams'));
    }

    public function lastYear()
    {
        $teams = Team::get();
        $initialTeams = Team::whereNull('final')->orWhere('final', '=', 0)->orderBy('name')->get();
        $finalTeams = Team::where('final', '=', 1)->orderBy('name')->get();
        return view('assessment.index')->with(compact('teams','initialTeams','finalTeams'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($team_id, $assessor_id)
    {
        $team = Team::find($team_id);
        $assessor = Assessor::find($assessor_id);
//        $goals = Goal::where('inactive','=', NULL->get();
        $goals = Goal::get();
        $courses = Course::orderBy('name')->get();
        $slos = Slo::where('team_id', '=', $team_id)->where('inactive','<>', 1)->get();
//        dd($goals);
        return view('assessment.create')->with(compact('team','assessor','goals','courses','slos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'course_id' => 'required',
        ]);
       $data = $request->except('_token');

        $assessment = new Assessment($data);
        $assessment->save();
//        dd($assessment);
        return redirect('assessment/' . $assessment->id);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Assessment  $assessment
     * @return \Illuminate\Http\Response
     */
    public function show(Assessment $assessment)
    {

        $team = Team::find($assessment->team_id);
        $assessor = Assessor::find($assessment->assessor_id);
        $selected_goal = Goal::find($assessment->goal_id);
        $selected_course= Course::find($assessment->course_id);
        $selected_slo = Slo::find($assessment->slo_id);

//        dd($selected_goal);
        return view('assessment.show')->with(compact('assessment','team','assessor','selected_goal','selected_slo'
        ,'selected_course'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Assessment  $assessment
     * @return \Illuminate\Http\Response
     */
    public function edit(Assessment $assessment)
    {
        $team = Team::find($assessment->team_id);
        $assessor = Assessor::find($assessment->assessor_id);
        $selected_goal = Goal::find($assessment->goal_id);
        $courses = Course::where('inactive','<>', 1)->orderBy('name')->get();
        $selected_slo = Slo::find($assessment->slo_id);
        $goals = Goal::where('inactive','<>', 1)->get();
        $slos = Slo::where('team_id', '=', $team->id)->where('inactive','<>', 1)->get();
//        dd($assessment);
        return view('assessment.edit')->with(compact('assessment','team','assessor','selected_goal'
        ,'goals','courses','selected_slo','slos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Assessment  $assessment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Assessment $assessment)
    {
        $data = $request->except('_token','_method');
        $assessment->update($data);
        return redirect('assessment/' . $assessment->id);
//        return redirect('dashboard/assessor/' . $assessment->id);
dd($assessment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Assessment  $assessment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $assessment = Assessment::destroy($id);
        return redirect( 'admin/delete_assessment');
    }
}
