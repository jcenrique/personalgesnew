<?php


namespace Database\Seeders;

use App\Models\Categoriaelemento;
use Illuminate\Database\Seeder;

use App\Models\Elementoinspeccion;

class InspeccionSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            'ORRATZAK' => [
                ['eu' => 'Ibilgetzeko elementuak dituzte', 'es' => 'Disponen de elementos de inmovilización'],
                ['eu' => 'Orratzaren tokiko aginteak funzionatzen du', 'es' => 'Funciona el mando local de aguja'],
                ['eu' => 'Eskuz funtzionatzen du (biraderarekin)', 'es' => 'Funciona manualmente (con manivela)'],
                ['eu' => 'Boure orratzaren eragingailuaren funtzionamendua (prozesu osoa egiaztatu)', 'es' => 'Funcionamiento del accionamiento de aguja boure (comprobar todo el proceso)'],
            ],

            'EBAKIGAILUAK' => [
                ['eu' => 'Ebakigailuaren tokiko aginteak funtzionatzen du. Maniobrak egitea', 'es' => 'Funciona el mando local de seccionador. Realizar maniobras'],
                ['eu' => 'Ibilgetze-elementuak ditu (eskuzkoak)', 'es' => 'Dispone de elemento de inmovilización (manuales)'],
                ['eu' => 'Eskuz eragiteko biradera du (motorduna)', 'es' => 'Dispone de manivela para accionamiento manual (motor.)'],
            ],

            'BLOKEO-KUTXAK' => [
                ['eu' => 'Ibilbideen ezarpena', 'es' => 'Establecimiento de itinerarios'],
                ['eu' => 'Sarrerako sarrailak', 'es' => 'Cerraduras de acceso'],
                ['eu' => 'Agintea hartzeko sarrailak', 'es' => 'Cerraduras de toma de mando'],
                ['eu' => 'Argizko adierazleak', 'es' => 'Indicaciones luminosas'],
            ],

            'KATIGAMENDUKO TAULA' => [
                ['eu' => 'Ibilbide orokorren ezarpena', 'es' => 'Establecimiento de itinerarios generales'],
                ['eu' => 'Maniobra-ibilbideen ezarpena', 'es' => 'Establecimiento de itinerarios de maniobras'],
                ['eu' => 'Argizko adierazleak', 'es' => 'Indicaciones luminosas'],
            ],

            'APARKATUTAKO MATERIALA' => [
                ['eu' => 'Ibilgetua deribaren kontrako kaltzeekin', 'es' => 'Inmovilizado con calces antideriva'],
                ['eu' => 'Jardundakoa aparkatzeko balaztarekin', 'es' => 'Actuado el freno de estacionamiento'],
            ],

            'MANIOBRAK' => [
                ['eu' => 'Irrati-aparatu eramangarria erabiltzen du', 'es' => 'Utiliza aparato de radio portátil'],
                ['eu' => 'Maniobra apartatuak eta denbora nahikoarekin esekiak', 'es' => 'Maniobras apartadas y suspendidas con tiempo suficiente'],
                ['eu' => 'Makinistak kabina egokia hartzen du', 'es' => 'El maquinista ocupa la cabina adecuada'],
            ],

            'DOKUMENTAZIOA' => [
                ['eu' => 'R.C.S du', 'es' => 'Tiene R.C.S'],
                ['eu' => '11/02 Agindua du', 'es' => 'Tiene Consigna 11/02'],
                ['eu' => 'Dokumentazio-liburuan ageri da', 'es' => 'Figura en libro documentación'],
                ['eu' => 'Dokumentazioa jasoa du', 'es' => 'Tiene recogida documentación'],
            ],
        ];

        $sort = 1;

        foreach ($categorias as $categoriaEu => $elementos) {

            // Crear o recuperar categoría
            $categoria = Categoriaelemento::firstOrCreate(
                ['nombre_eu' => $categoriaEu],
                [
                    'nombre_es' => $this->traducirCategoria($categoriaEu),
                    'sort' => $sort++,
                    'active' => 1,
                ]
            );

            // Insertar elementos
            foreach ($elementos as $el) {
                Elementoinspeccion::firstOrCreate(
                    [
                        'categoriaelemento_id' => $categoria->id,
                        'nombre_eu' => $el['eu'],
                    ],
                    [
                        'nombre_es' => $el['es'],
                        'active' => 1,
                    ]
                );
            }
        }
    }

    private function traducirCategoria($eu)
    {
        return [
            'ORRATZAK' => 'AGUJAS',
            'EBAKIGAILUAK' => 'SECCIONADORES',
            'BLOKEO-KUTXAK' => 'CAJAS DE BLOQUEO',
            'KATIGAMENDUKO TAULA' => 'CUADRO ENCLAVAMIENTO',
            'APARKATUTAKO MATERIALA' => 'MATERIAL ESTACIONADO',
            'MANIOBRAK' => 'MANIOBRAS',
            'DOKUMENTAZIOA' => 'DOCUMENTACIÓN',
        ][$eu] ?? $eu;
    }
}
