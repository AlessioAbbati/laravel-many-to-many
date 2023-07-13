<?php

namespace App\Http\Controllers\Admin;

use App\Models\Type;
use App\Models\Project;
use App\Models\Technology;
// use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    private $validations = [
        'title'             => 'required|string|max:50',
        'type_id'           => 'required|integer|exists:types,id',
        'author'            => 'required|string|max:30',
        'creation_date'     => 'required|date',
        'last_update'       => 'required|date',
        'collaborators'     => 'nullable|string|max:150',
        'description'       => 'string',
        'image'             => 'nullable|image|max:1024',
        'link_github'       => 'required|url|max:200',
        'technologies'      => 'nullable|array',
        'technologies. *'   => 'integer|exists:technologies,id',
        
    ];

    private $validations_messages = [
        'required'      => 'il campo :attribute è obbligatorio',
        'max'           => 'il campo :attribute deve avere almeno :max caratteri',
        'url'           => 'il campo deve essere un url valido',
        'exists'        => 'Valore non valido',
    ];

    
    public function index()
    {
        $projects = Project::paginate(3);
        return view('admin.projects.index', compact('projects'));
    }

    
    public function create()
    {
        $types = Type::all();
        $technologies = Technology::all();
        return view('Admin.projects.create', compact('types', 'technologies'));
    }

    
    public function store(Request $request)
    {
        // validare i dati 
        $request->validate($this->validations, $this->validations_messages);

        $data = $request->all();

        // salvare l'immagine nella cartella degli uploads
        // prendere il percorso della nuova immagine
        $imagePath = Storage::put('uploads', $data['image']);
        
        // Salvare i dati nel database
        $newProject = new Project();
        $newProject->title          = $data['title'];
        $newProject->slug           = Project::slugger($data['title']);
        $newProject->type_id        = $data['type_id'];
        $newProject->author         = $data['author'];
        $newProject->creation_date  = $data['creation_date'];
        $newProject->last_update    = $data['last_update'];
        $newProject->collaborators  = $data['collaborators'];
        $newProject->description    = $data['description'];
        $newProject->image          = $imagePath;
        // $newProject->languages      = $data['languages'];
        $newProject->link_github    = $data['link_github'];
        $newProject->save();

        // associo itag
        $newProject->technologies()->sync($data['technologies'] ?? []);

        return redirect()->route('admin.project.show', ['project' => $newProject]);
    }

    
    public function show($slug) // per vedere la show del trashed aggiungi withTrashed()-> prima del where
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        return view('admin.projects.show', compact('project'));
    }

   
    public function edit($slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $types = Type::all();
        $technologies = Technology::all();
        return view('admin.projects.edit', compact('project', 'types', 'technologies'));
    }

    
    public function update(Request $request, $slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();

        // validare i dati 
        $request->validate($this->validations, $this->validations_messages);

        $data = $request->all();

        if($data['image']) {
            // salvare l'eventuale nuova immagine
            $imagePath = Storage::put('uploads', $data['image']);

            if($project->image) {
                // eliminare la vecchia immagine
                Storage::delete($project->image);
            }
            
            // aggiorno il valore della nuova immagine
            $project->image  = $imagePath;
        }
        
        
        $project->title             = $data['title'];
        $project->type_id           = $data['type_id'];
        $project->author            = $data['author'];
        $project->creation_date     = $data['creation_date'];
        $project->last_update       = $data['last_update'];
        $project->collaborators     = $data['collaborators'];
        $project->description       = $data['description'];
        // $project->languages         = $data['languages'];
        $project->link_github       = $data['link_github'];
        $project->update();

        $project->technologies()->sync($data['technologies'] ?? []);

        return redirect()->route('admin.project.show', ['project' => $project]);
    }

   
    public function destroy($slug)
    {

        $project = Project::where('slug', $slug)->firstOrFail();

        $project->delete();

        return to_route('admin.project.index')->with('delete_success', $project);
    }

    public function restore($slug)
    {
        $project = Project::find($slug);
        Project::withTrashed()->where('slug', $slug)->restore();
        $project = Project::where('slug', $slug)->firstOrFail();

        

        return to_route('admin.project.trashed')->with('restore_success', $project);
    }

    public function cancel($slug)
    {
        $project = Project::find($slug);
        Project::withTrashed()->where('slug', $slug)->restore();
        $project = Project::where('slug', $slug)->firstOrFail();

        

        return to_route('admin.project.index')->with('cancel_success', $project);
    }

    public function trashed()
    {
        // $projects = project::all(); // SELECT * FROM `projects`
        $trashedProjects = Project::onlyTrashed()->paginate(3);

        return view('admin.projects.trashed', compact('trashedProjects'));
    }

    public function harddelete($slug)
    {
        $project = Project::withTrashed()->where('slug', $slug)->first();

        // cancello i percorsi immagine DA SISTEMARE
        if ($project->file) {
            Storage::delete($project->file);
        }
        
        // se ho il trashed lo inserisco nel harddelete
        $project->technologies()->detach();
        $project->forceDelete();
        return to_route('admin.project.trashed')->with('delete_success', $project);
    }
}
