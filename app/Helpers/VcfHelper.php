<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class VcfHelper
{
    function generateVcf(array $contact): string
    {
        $vcf = <<<VCF
                BEGIN:VCARD
                VERSION:3.0
                FN:{$contact['name']}
                ORG:{$contact['company']}
                TEL;TYPE=CELL:{$contact['phone']}
                EMAIL:{$contact['email']}
                END:VCARD
                VCF;

        return $vcf;
    }

    function generateAndSaveVcf(array $contact, string $fileName): string
    {
        $vcf = $this->generateVcf($contact);

        $path = "vcards/{$fileName}.vcf";

        Storage::disk('public')->put($path, $vcf);

        return Storage::url($path);
    }
}
