<?php

namespace App\Controllers;

class RajaOngkirController extends BaseController
{
    private $api_key = 'oZzVhbTZ5db277dc853b42f9zwTyLefw';

    public function __construct()
    {
        $env_key = getenv('RAJAONGKIR_API_KEY');
        if ($env_key) {
            $this->api_key = $env_key; 
        }
    }

    // 1. Endpoint Mencari Destinasi (Kelurahan/Kecamatan)
    public function cari_lokasi()
    {
        $keyword = $this->request->getGet('search');
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search=" . urlencode($keyword) . "&limit=50",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            // Bypass SSL biar aman di Localhost XAMPP/Laragon
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                "key: " . $this->api_key,
                "Accept: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return $this->response->setContentType('application/json')->setBody($response);
    }

    // 2. Endpoint Menghitung Ongkos Kirim
    public function hitung_ongkir()
    {
        // Default Origin 64999 (Pedurungan Tengah, Semarang)
        $origin = 64999; 
        $destination = $this->request->getPost('destination_id');
        $weight = 1000; 
        $courier = $this->request->getPost('kurir');

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query([
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier
            ]),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                "key: " . $this->api_key,
                "Content-Type: application/x-www-form-urlencoded",
                "Accept: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            // Biar ngeluarin error jelas, nggak cuma mati mendadak
            return $this->response->setJSON(['status' => 'error', 'message' => 'System error: ' . $err]);
        }

        return $this->response->setContentType('application/json')->setBody($response);
    }
}