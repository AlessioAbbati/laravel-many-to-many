<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use Illuminate\Http\Request;

class TechnologyController extends Controller
{
    private $validations = [
        'name'         => 'required|string|max:20',
    ];
    
    public function index()
    {
        $technologies = Technology::all();
        return view('admin.technologies.index', compact('technologies'));
    }

    
    public function create()
    {
        return view('Admin.technologies.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->validations);

        $data = $request->all();
        // Salvare i dati nel database
        $newTechnology = new Technology();
        $newTechnology->name          = $data['name'];
        $newTechnology->save();

        

        return redirect()->route('admin.technology.show', ['technology' => $newTechnology]);
    }

    
    public function show(Technology $technology)
    {
        return view('admin.technologies.show', compact('technology'));
    }

   
    public function edit(Technology $technology)
    {
        return view('admin.technologies.edit', compact('technology'));
    }

    
    public function update(Request $request, Technology $technology)
    {
        $request->validate($this->validations);

        $data = $request->all();
        
        $technology->name          = $data['name'];
        $technology->save();

        

        return redirect()->route('admin.technology.show', ['technology' => $technology]);
    }

   
    public function destroy(Technology $technology)
    {
        $technology->projects()->detach();
        $technology->delete(); 
        return to_route('admin.technology.index')->with('technology_delete_success', $technology);
    }
}
