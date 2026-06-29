<?php

namespace Tests\Feature;

use App\Enum\StatusSolicitudes;
use App\Models\Disfrute;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SolicitudesTest extends TestCase
{
    public function test_required_fields_validation_for_solicitud(): void
    {
        $validator = Validator::make([], [
            'razon' => ['required'],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('razon', $validator->errors()->toArray());
    }

    public function test_aprobar_solicitud_changes_status(): void
    {
        $disfrute = new Disfrute([
            'status' => StatusSolicitudes::Solicitado,
        ]);

        $disfrute->status = StatusSolicitudes::Aprobado;

        $this->assertSame(StatusSolicitudes::Aprobado, $disfrute->status);
    }

    public function test_rechazar_solicitud_changes_status(): void
    {
        $disfrute = new Disfrute([
            'status' => StatusSolicitudes::Solicitado,
        ]);

        $disfrute->status = StatusSolicitudes::Rechazado;

        $this->assertSame(StatusSolicitudes::Rechazado, $disfrute->status);
    }
}