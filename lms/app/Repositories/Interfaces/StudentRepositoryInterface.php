<?php

namespace App\Repositories\Interfaces;

interface StudentRepositoryInterface
{
    /**
     * Tüm öğrencileri getirir.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

    /**
     * Belirli bir öğrenci kaydını ID ile bulur.
     *
     * @param  int  $id
     * @return \App\Models\Student
     */
    public function findById($id);

    /**
     * Yeni bir öğrenci kaydı oluşturur.
     *
     * @param  array  $data
     * @return \App\Models\Student
     */
    public function create(array $data);

    /**
     * Mevcut bir öğrenci kaydını günceller.
     *
     * @param  int    $id
     * @param  array  $data
     * @return \App\Models\Student
     */
    public function update($id, array $data);

    /**
     * Belirli bir öğrenci kaydını siler.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete($id);
}
