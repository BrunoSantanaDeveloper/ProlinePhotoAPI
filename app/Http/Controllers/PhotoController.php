<?php

namespace App\Http\Controllers;

use App\Services\PhotoService;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    protected $photoService;

    public function __construct(PhotoService $photoService)
    {
        $this->photoService = $photoService;
    }

    public function index()
    {
        return response()->json($this->photoService->getAllPhotos());
    }

    public function show($id)
    {
        $photo = $this->photoService->getPhotoById($id);
        if ($photo) {
            return response()->json($photo);
        }
        return response()->json(['message' => 'Photo not found'], 404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|file|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $userId = auth()->id(); // Obtém o ID do usuário autenticado

        $data = $request->only(['latitude', 'longitude']);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
        }
        $data['user_id'] = $userId; // Adiciona o ID do usuário aos dados

        $photo = $this->photoService->createPhoto($data);
        return response()->json($photo, 201);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'photo' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $data = $request->all();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo');
        }

        $photo = $this->photoService->updatePhoto($id, $data);
        if ($photo) {
            return response()->json($photo);
        }
        return response()->json(['message' => 'Photo not found'], 404);
    }

    public function destroy($id)
    {
        $photo = $this->photoService->deletePhoto($id);
        if ($photo) {
            return response()->json(['message' => 'Photo deleted']);
        }
        return response()->json(['message' => 'Photo not found'], 404);
    }
}
