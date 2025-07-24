<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Conversation/Index', [
            'name' => 'Wes',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isOrganization()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isOrganization()) {
            abort(403, 'Unauthorized action.');
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
        if (!auth()->user()->isAdmin() && !auth()->user()->isOrganization()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isOrganization()) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isOrganization()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
