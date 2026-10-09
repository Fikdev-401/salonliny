<?php

namespace App\Models\Concerns;

use App\Models\Cabang;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait HasCabangScope
{
    protected static function bootHasCabangScope(): void
    {
        static::addGlobalScope('cabang', function (Builder $builder): void {
            if (!Auth::hasUser()) {
                return;
            }

            if (!Schema::hasColumn($builder->getModel()->getTable(), 'cabang_id')) {
                return;
            }

            $user = Auth::user();

            if (!$user || $user->isSuperAdmin()) {
                return;
            }

            if ($user->cabang_id) {
                $builder->where($builder->qualifyColumn('cabang_id'), $user->cabang_id);
                return;
            }

            $builder->whereNull($builder->qualifyColumn('cabang_id'));
        });

        static::creating(function ($model): void {
            if (!Schema::hasColumn($model->getTable(), 'cabang_id')) {
                return;
            }

            if (!empty($model->cabang_id)) {
                return;
            }

            $model->cabang_id = static::resolveCurrentCabangId();
        });
    }

    public function scopeForCabang(Builder $query, ?int $cabangId): Builder
    {
        if ($cabangId === null) {
            return $query->whereNull($query->qualifyColumn('cabang_id'));
        }

        return $query->where($query->qualifyColumn('cabang_id'), $cabangId);
    }

    protected static function resolveCurrentCabangId(): ?int
    {
        if (Auth::hasUser() && Auth::user()?->cabang_id) {
            return (int) Auth::user()->cabang_id;
        }

        if (!Schema::hasTable('cabangs')) {
            return null;
        }

        return Cabang::query()
            ->where('is_default', true)
            ->where('status', 'aktif')
            ->value('id');
    }
}
