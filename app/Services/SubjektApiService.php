<?php

namespace App\Services;

use App\TaxpayerType;
use Illuminate\Support\Facades\Http;

class SubjektApiService
{
    private const BASE_URL = 'https://api.subjekt.sk/v1';

    /**
     * @return array<int, array<string, mixed>>
     */
    public function search(string $query, int $limit = 30): array
    {
        $query = trim($query);

        if (mb_strlen($query) < 2) {
            return [];
        }

        $response = Http::timeout(8)
            ->acceptJson()
            ->get(self::BASE_URL.'/search', [
                'q' => $query,
                'country' => 'sk',
                'limit' => $limit,
            ]);

        if (! $response->successful()) {
            return [];
        }

        return collect($response->json('results', []))
            ->map(fn (array $result) => [
                'ico' => (string) ($result['ico'] ?? ''),
                'name' => (string) ($result['name'] ?? ''),
                'city' => (string) ($result['address']['city'] ?? ''),
            ])
            ->all();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function entity(string $ico): ?array
    {
        $ico = preg_replace('/\D/', '', $ico) ?? '';

        if (strlen($ico) !== 8) {
            return null;
        }

        $response = Http::timeout(8)
            ->acceptJson()
            ->get(self::BASE_URL.'/entity/'.$ico, [
                'country' => 'sk',
            ]);

        if (! $response->successful()) {
            return null;
        }

        return $response->json();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    public function mapToFormFields(array $data): array
    {
        $address = $data['address'] ?? [];
        $registration = $data['registration'] ?? [];

        $street = trim(implode(' ', array_filter([
            $address['street'] ?? null,
            $address['building_no'] ?? null,
        ])));

        $registryParts = array_filter([
            $registration['office'] ?? null,
            $registration['number'] ?? null,
        ]);

        $icDph = $data['ic_dph'] ?? null;
        $dic = $data['dic'] ?? null;

        $taxpayerType = TaxpayerType::NeplatitelDph;

        if (filled($icDph)) {
            $taxpayerType = TaxpayerType::PlatitelDph;
        }

        return [
            'name' => (string) ($data['name'] ?? ''),
            'street' => $street,
            'postal_code' => (string) ($address['zip'] ?? ''),
            'city' => (string) ($address['city'] ?? ''),
            'country' => (string) ($address['country'] ?? 'SK'),
            'ico' => (string) ($data['ico'] ?? ''),
            'dic' => (string) ($dic ?? ''),
            'ic_dph' => (string) ($icDph ?? ''),
            'taxpayer_type' => $taxpayerType->value,
            'registry' => implode(', ', $registryParts),
        ];
    }
}
