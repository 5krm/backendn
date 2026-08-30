<?php

namespace App\Filament\Tutor\Resources\Certificates\Pages;

use App\Filament\Tutor\Resources\Certificates\CertificateResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCertificate extends ViewRecord
{
    protected static string $resource = CertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No actions - certificates are read-only
        ];
    }
}
