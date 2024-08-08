<?php
namespace App\Repositories;

use App\Models\Photo;
use App\Repositories\Contracts\PhotoRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class PhotoRepository implements PhotoRepositoryInterface
{
    public function all()
    {
        return Photo::all();
    }

    public function find($id)
    {
        return Photo::find($id);
    }

    public function create(array $data)
    {
        if (isset($data['photo'])) {
            $photoPath = $data['photo']->store('photos', 'public');
            $data['photo_path'] = $photoPath;
        }

        return Photo::create($data);
    }

    public function update($id, array $data)
    {
        $photo = Photo::find($id);

        if ($photo) {
            if (isset($data['photo'])) {
                // Delete the old photo file
                if ($photo->photo_path && Storage::disk('public')->exists($photo->photo_path)) {
                    Storage::disk('public')->delete($photo->photo_path);
                }

                // Store the new photo file
                $photoPath = $data['photo']->store('photos', 'public');
                $data['photo_path'] = $photoPath;
            }

            $photo->update($data);
        }

        return $photo;
    }

    public function delete($id)
    {
        $photo = Photo::find($id);

        if ($photo) {
            if ($photo->photo_path && Storage::disk('public')->exists($photo->photo_path)) {
                Storage::disk('public')->delete($photo->photo_path);
            }

            $photo->delete();
        }

        return $photo;
    }
}
