<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Twilio\Rest\Client;


class EmployerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employers = Employer::whereStatus(1)->orderByDesc('created_at');
        if (!is_null($request->search)) {
            return $this->search($employers, $request->search);
        }
        $employers = $employers->paginate(20);
        return view('employer.index', compact('employers'));
    }

    public function search($query, $search)
    {
        return response()->json($query->where('name', 'LIKE', "%$search%")->paginate(20));
    }

    public function getEmployerJson(Request $request)
    {
        $data = Employer::whereStatus(1)->orderByDesc('created_at');
        if (!is_null($request->search)) {
            return $this->search($data, $request->search);
        }
        $data = $data->paginate(20);
        return response()->json($data);
    }

    public function changeStatus(Employer $employer)
    {
        $employer->update(['status' => !$employer->status]);
        return redirect()->back();
    }
    public function changeStatusPosition(Position $position)
    {
        $position->update(['status' => !$position->status]);
        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $positions = Position::whereStatus(1)->get();
        return view('employer.create', ['positions' => $positions]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'image|max:4096',
        ]);
        $inputs = $request->all();
        if (!is_null($request->image)) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
            $inputs['image'] = $imageName;
        }
        $password = $inputs['password'];
        $inputs['password'] = Hash::make($inputs['password']);
        $inputs['uuid'] = Str::uuid();
        $data = Employer::create($inputs);
//        $this->sendSMS($data->phone, $password);
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Employer $employer)
    {
        return view('employer.show', compact('employer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employer $employer)
    {
        return view('employer.edit', compact('employer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employer $employer)
    {
        $inputs = $request->all();
        $data = $employer->update($inputs);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employer $employer)
    {
        $employer->delete();
        return redirect()->back();
    }

    private function sendSMS($phone, $password)
    {
        $accountSid = env('TWILIO_SID');
        $authToken = env('TWILIO_TOKEN');
        $fromNumber = env('TWILIO_FROM');
        $client = new Client($accountSid, $authToken);

        $client->messages->create(
            $phone,
            [
                'from' => $fromNumber,
                'body' => 'Խնդրում ենք չուղարկել ոչ մեկի, սա Ձեր գաղտնաբառն է => ' . $password
            ]
        );
    }

    public function myProfile()
    {
        return view('employer.account');
    }

    public function createPositionView()
    {
        return view('positions.create');
    }
    public function updatePositionsView(Position $position)
    {
        return view('positions.edit', compact('position'));
    }

    public function createPosition(Request $request)
    {
        Position::create(['name' => $request->name, 'status' => $request->status]);
        return redirect()->back();
    }
    public function updatePositions($id, Request $request)
    {
        $position = Position::findOrFail($id)->update([
            'name' => $request->name,
        ]);
        return redirect()->back();
    }
    public function deletePositions($id)
    {
        $position = Position::findOrFail($id)->delete();
        return redirect()->back();
    }
}
