<?php

namespace App\Http\Resources;

use App\Http\Resources\Parents\Resource;
use App\Models\Path;
use App\Models\UserPath;

class PathResource extends Resource
{
    public function small(): PathResource
    {
        $this->attributes = [
            'id', 'name', 'key_name', 'is_subpath', 'prepend'
        ];

        return $this;
    }

    public function regular(): PathResource
    {
        $this->small();
        return $this;
    }

    public function extended(): PathResource
    {
        $this->regular();

        $this->addUsersCountToResource();
        $this->addParentToResource();

        return $this;
    }

    private function addUsersCountToResource()
    {
        $paths_ids = $this->resources->pluck('id');
        $users_paths = UserPath::query()->selectRaw('users_paths.*')
            ->join('users', 'users.id', '=', 'users_paths.user_id')
            ->join('rookies', 'rookies.id', '=', 'users.id')
            ->join('photos', 'users.id', '=', 'photos.user_id')
            ->whereIn('users_paths.path_id', $paths_ids)
            ->where('users.active', true)
            ->where('photos.main', true)
            ->where('rookies.is_converter', false)
            ->groupBy('users_paths.id')
            ->get();

        foreach ($this->resources as $resource) {
            $users_path = $users_paths->where('path_id', $resource->id);
            $resource->users_count = $users_path->count();
        }

        $this->attributes[] = 'users_count';
    }

    private function addParentToResource()
    {
        $paths_ids = $this->resources->where('is_subpath', true)->pluck('parent_id');
        $paths = Path::query()
            ->where('is_subpath', false)
            ->whereIn('id', $paths_ids)
            ->get();

        $paths_resources = PathResource::compute(
            $this->request,
            $paths,
            'small'
        )->get();

        foreach ($this->resources as $resource) {
            $resource->parent = ($resource->is_subpath)
                ? $paths_resources->where('id', $resource->parent_id)->first()
                : null;
        }

        $this->attributes[] = 'parent';
    }
}
