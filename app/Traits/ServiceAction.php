<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait ServiceAction
{
    abstract protected function getModelClass(): string;
    abstract protected function getRequestClass(): string;

    public function store()
    {
        $request = app($this->getRequestClass());
        $data = $request->validated();

        DB::beginTransaction();
        try {
            if (method_exists($this, 'beforeStore')) {
                $data = $this->beforeStore($data);
            }

            $model = $this->getModelClass()::create($data);

            if (method_exists($this, 'afterStore')) {
                $this->afterStore($model, $data);
            }

            DB::commit();


            if (method_exists($this, 'redirectAfterStore')) {
                return $this->redirectAfterStore($model, $data);
            }

            return redirect()->back()
                ->with('success', 'Data berhasil ditambahkan, Yang Mulia!');

        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['message' => 'Gagal menyimpan: ' . $th->getMessage()]);
        }
    }

    public function update($id)
    {
        $model = $this->getModelClass()::findOrFail($id);
        $request = app($this->getRequestClass());
        $data = $request->validated();

        DB::beginTransaction();
        try {
            if (method_exists($this, 'beforeUpdate')) {
                $data = $this->beforeUpdate($model, $data);
            }

            $model->update($data);

            if (method_exists($this, 'afterUpdate')) {
                $this->afterUpdate($model, $data);
            }

            DB::commit();

            if (method_exists($this, 'redirectAfterUpdate')) {
                return $this->redirectAfterUpdate($model, $data);
            }

            return redirect()->back()
                ->with('success', 'Data berhasil diperbarui, Yang Mulia!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['message' => 'Gagal update: ' . $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $model = $this->getModelClass()::findOrFail($id);

        DB::beginTransaction();
        try {
            if (method_exists($this, 'beforeDestroy')) {
                $this->beforeDestroy($model);
            }

            $model->delete();

            if (method_exists($this, 'afterDestroy')) {
                $this->afterDestroy($model);
            }

            DB::commit();

            if (method_exists($this, 'redirectAfterDestroy')) {
                return $this->redirectAfterDestroy($model);
            }

            return redirect()->back()
                ->with('success', 'Data berhasil dihapus.');

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['message' => 'Gagal hapus: ' . $th->getMessage()]);
        }
    }
}
