<?php

if (!function_exists('asset_url')) {
    /**
     * Genera la URL de un asset con un token de versión basado en la fecha de modificación.
     *
     * @param string $path Ruta relativa dentro de /assets (ej: 'css/app.css')
     * @return string URL completa con versión (?v=timestamp)
     */
    function asset_url(string $path): string
    {
        $filePath = FCPATH . 'assets/' . $path;

        // Si existe el archivo, usamos su fecha de modificación; si no, el timestamp actual
        $version = file_exists($filePath) ? filemtime($filePath) : time();

        return site_url('assets/' . $path) . '?v=' . $version;
    }

    function asset_url2(string $path): string
    {
        $filePath = FCPATH . $path;

        // Si existe el archivo, usamos su fecha de modificación; si no, el timestamp actual
        $version = file_exists($filePath) ? filemtime($filePath) : time();

        return site_url($path) . '?v=' . $version;
    }
}
