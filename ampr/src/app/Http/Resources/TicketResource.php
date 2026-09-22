<?php

namespace App\Http\Resources;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Konfigurasi QR Code (Sama seperti di Controller sebelumnya)
        // Kita generate QR hanya jika statusnya 'booked' (Aktif) agar performa history tetap cepat
        $qrImage = null;

        if ($this->status === 'booked') {
            $options = new QROptions([
                'version' => 5,
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel' => QRCode::ECC_L,
                'scale' => 5,
                'imageBase64' => true,
            ]);

            $qrImage = (new QRCode($options))->render($this->booking_code);
        }

        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'start_time' => $this->start_time, // Biarkan Carbon yang handle format di Vue atau disini
            'end_time' => $this->end_time,
            'status' => $this->status,

            // Ini Data QR Code (Base64 Image) yang dikirim ke Vue
            'qr_code_image' => $qrImage,
        ];
    }
}
