<?php

namespace App\Repositories\Admin;

use App\Models\Blogs;
use App\Repositories\Admin\BlogRepositoryInterface;

class BlogRepository implements BlogRepositoryInterface
{
    public function getLatest($limit)
    {
        // Lấy các bài blog mới nhất
        return Blogs::latest() // <-- *** XÓA with('user:id,name') ***
                    ->take($limit)
                    ->get();
    }

    public function adminGetAll($perPage)
    {
        // Lấy tất cả bài blog, phân trang, cho trang admin
        return Blogs::latest() // <-- *** XÓA with('user:id,name') ***
                    ->paginate($perPage);
    }

    public function findById($id)
    {
        return Blogs::find($id); // <-- *** XÓA with('user:id,name') ***
    }

    public function create(array $data)
    {
        return Blogs::create($data);
    }

    public function update($id, array $data)
    {
        $blog = $this->findById($id);
        if ($blog) {
            $blog->update($data);
            return $blog;
        }
        return null;
    }

    public function delete($id)
    {
        $blog = $this->findById($id);
        if ($blog) {
            return $blog->delete();
        }
        return false;
    }
}