<?php

namespace App\Http\Controllers;

use App\Models\PublishSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $items = PublishSchedule::where('user_id', Auth::id())
            ->orderBy('weekday')->orderBy('time')->get();
        return view('schedules.index', compact('items'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'weekday' => 'required|integer|min:0|max:6',
            'time'    => 'required|date_format:H:i',
        ]);

        PublishSchedule::create([
            'user_id' => Auth::id(),
            'weekday' => $r->integer('weekday'),
            'time'    => $r->input('time').':00',
            'active'  => true,
        ]);

        return back()->with('status','Horario agregado.');
    }

    public function update(Request $r, $id)
    {
        $item = PublishSchedule::where('user_id', Auth::id())->findOrFail($id);
        $r->validate([
            'weekday' => 'required|integer|min:0|max:6',
            'time'    => 'required|date_format:H:i',
            'active'  => 'nullable|boolean',
        ]);
        $item->update([
            'weekday' => $r->integer('weekday'),
            'time'    => $r->input('time').':00',
            'active'  => $r->boolean('active', true),
        ]);
        return back()->with('status','Horario actualizado.');
    }

    public function destroy($id)
    {
        PublishSchedule::where('user_id', Auth::id())->where('id',$id)->delete();
        return back()->with('status','Horario eliminado.');
    }
}
