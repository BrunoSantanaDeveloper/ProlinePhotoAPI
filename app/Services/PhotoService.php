<?php
namespace App\Services;

use App\Repositories\Contracts\PhotoRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class PhotoService
{
    protected $photoRepository;

    public function __construct(PhotoRepositoryInterface $photoRepository)
    {
        $this->photoRepository = $photoRepository;
    }

    public function getAllPhotos()
    {
        return $this->photoRepository->all();
    }

    public function getPhotoById($id)
    {
        return $this->photoRepository->find($id);
    }

    public function createPhoto(array $data)
    {
        if (!isset($data['photo'])) {
            throw new \Exception('No photo provided.');
        }

        $photo = $data['photo'];
        $userId = $data['user_id'];

        $filename = 'user_' . $userId . '_' . time() . '.' . $photo->getClientOriginalExtension();
        $path = $photo->storeAs('photos', $filename, 'public');

        $data['photo_path'] = $path;

        return $this->photoRepository->create($data);
    }

    public function updatePhoto($id, array $data)
{
    if (isset($data['photo'])) {
        $photo = $data['photo'];
        $userId = $data['user_id'];

        $filename = 'user_' . $userId . '_' . time() . '.' . $photo->getClientOriginalExtension();
        $path = $photo->storeAs('photos', $filename, 'public');

        $data['photo_path'] = $path;
    }

    return $this->photoRepository->update($id, $data);
}


public function deletePhoto($id)
{
    $photo = $this->photoRepository->find($id);

    if (!$photo) {
        throw new \Exception('Photo not found.');
    }

    Storage::disk('public')->delete($photo->photo_path);

    return $this->photoRepository->delete($id);
}

}
