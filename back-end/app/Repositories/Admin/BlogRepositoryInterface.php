<?php

namespace App\Repositories\Admin;

interface BlogRepositoryInterface
{
    public function getLatest($limit);
    public function adminGetAll($perPage);
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}