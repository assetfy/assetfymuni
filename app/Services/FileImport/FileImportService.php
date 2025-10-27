<?php

namespace App\Services\FileImport;

class FileImportService extends ProcessFile
{
    protected $geocodingService;

    /**
     * Inyecta el servicio de geocodificación.
     *
     * @param GeocodingService $geocodingService
     */
    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }
}
