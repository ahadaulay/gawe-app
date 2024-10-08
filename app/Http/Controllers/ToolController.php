<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreToolRequest;
use App\Http\Requests\UpdateToolRequest;

class ToolController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tools = Tool::all();
        return view('admin.tools.index', compact('tools'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tools.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreToolRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            if($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('icons', 'public');
                $validated['icon'] = $iconPath;
            }

            $validated['slug']= Str::slug($validated['name']);

            $newTools = Tool::create($validated);
        });

        return redirect()->route('admin.tools.index')->with('success', 'Tools created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tool $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tool $tool)
    {
        return view('admin.tools.edit', compact('tool'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateToolRequest $request, Tool $tool)
    {
        DB::transaction(function () use ($request,$tool) {
            $validated = $request->validated();

            if($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('icons', 'public');
                $validated['icon'] = $iconPath;
            }

            $validated['slug']= Str::slug($validated['name']);

            $newCategory = $tool->update($validated);
        });

        return redirect()->route('admin.tools.index')->with('success', 'Tools updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tool $tools)
    {
        DB::beginTransaction();
        try{
            $tools->delete();
            DB::commit();
            return redirect()->route('admin.categories.index')->with('success', 'Tools deleted successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('admin.categories.index')->with('error', 'Tools not deleted');
        }
    }
}
