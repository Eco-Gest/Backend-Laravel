<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::get();
        return response()->json($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $this->userService->getUser();

        $validated = $request->validate([
            'tag' => "required|string",
        ]);

        $tag = Tag::create([
            'label' => $request['tag'],
        ]);
        
        $tag->save();
        return response()->json($tag);
    }


    

    /**
     * Display the specified resource.
     */
    public function show(string $tag)
    {
        $user = auth()->user();
        $userAuthenticated = auth()->user();
        
        if (!$userAuthenticated || !$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $tag = Tag::where('label', $tag )->firstOrFail();
        if (!$tag) {
            return response()->json(['error' => 'Tag not found.'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $tagId)
    {
        $tag = Tag::where('id', $tagId)->firstOrFail();
        $tag->delete();
    }
}
