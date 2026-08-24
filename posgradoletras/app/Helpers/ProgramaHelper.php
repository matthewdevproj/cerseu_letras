<?php

namespace App\Helpers;

use App\Models\Programa;

class ProgramaHelper
{
    public static function getAllProgramas()
    {
        return Programa::activos()->orderBy('nombre')->get();
    }

    public static function getTalleres()
    {
        return Programa::activos()->talleres()->orderBy('nombre')->get();
    }

    public static function getCursos()
    {
        return Programa::activos()->cursos()->orderBy('nombre')->get();
    }

    public static function getProgramaBySlug($slug)
    {
        return Programa::where('slug', $slug)
            ->where('is_active', 1)
            ->first();
    }

    public static function searchProgramas($query)
    {
        return Programa::activos()
            ->where(function ($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                    ->orWhere('mencion', 'like', "%{$query}%")
                    ->orWhere('presentacion', 'like', "%{$query}%")
                    ->orWhere('sumilla', 'like', "%{$query}%");
            })
            ->orderBy('nombre')
            ->get();
    }
}
