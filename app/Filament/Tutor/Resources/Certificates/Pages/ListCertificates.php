<?php

namespace App\Filament\Tutor\Resources\Certificates\Pages;

use App\Filament\Tutor\Resources\Certificates\CertificateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCertificates extends ListRecords
{
    protected static string $resource = CertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Certificates are automatically generated when students pass exams
        ];
    }

    public function getHeading(): string
    {
        return __('tutor.certificates.heading');
    }

    public function getSubheading(): ?string
    {
        return __('tutor.certificates.subheading');
    }
}
