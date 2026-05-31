<?php

namespace App\Http\Controllers\Propietario;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Propietario\Propietario;
use Illuminate\Support\Facades\DB;

class PropietarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $propietarios = Propietario::query()
            ->when($request->filled('estado'), function ($query) use ($request) {
                $query->where('estado', $request->boolean('estado'));
            })
            ->orderBy('fecha_registro', 'desc')
            ->get();

        // Si la petición solicita JSON (API), devolver un arreglo simple con id/nombre
        if ($request->wantsJson() || $request->ajax()) {
            $simple = $propietarios->map(function ($p) {
                return [
                    'id' => $p->id_propietario ?? $p->id ?? null,
                    'nombre' => $p->nombre ?? $p->nombre_completo ?? null,
                ];
            });
            return response()->json($simple);
        }

        return view('dash.recepcion.propietarios', compact('propietarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validación básica
          $validated = $request->validate(
    [
        'nombre' => [
            'required',
            'string',
            'max:30',
            'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/'
        ],

        'telefono' => [
            'required',
         
            'regex:/^[0-9]{10}$/'
        ],

        'direccion' => [
            'nullable',
            'string',
            'min:10',
            'max:255'
        ],

        'correo_electronico' => [
            'nullable',
            'email',
            'max:255'
        ]
    ],
    [
        'nombre.required' => 'El nombre del propietario es obligatorio.',
        'nombre.max' => 'El nombre no puede exceder 30 caracteres.',
        'nombre.regex' => 'El nombre solo puede contener letras y espacios.',

        'telefono.required' => 'El teléfono es obligatorio.',
        'telefono.regex' => 'El teléfono debe contener exactamente 10 dígitos numéricos.',

        'direccion.min' => 'La dirección debe contener al menos 10 caracteres.',
        'direccion.max' => 'La dirección no puede exceder 255 caracteres.',

        'correo_electronico.email' => 'Ingrese un correo electrónico válido.',
        'correo_electronico.max' => 'El correo electrónico no puede exceder 255 caracteres.'
    ]
);
            
            // Verificar qué columnas existen en la tabla
            $columns = \DB::select("SHOW COLUMNS FROM propietario");
            $columnNames = array_column($columns, 'Field');
            
            // Preparar datos solo con columnas que existen
            $data = [];
            if (in_array('nombre', $columnNames)) $data['nombre'] = $validated['nombre'];
            if (in_array('telefono', $columnNames)) $data['telefono'] = $validated['telefono'];
            if (in_array('direccion', $columnNames)) $data['direccion'] = $validated['direccion'] ?? '';
            if (in_array('correo_electronico', $columnNames)) $data['correo_electronico'] = $validated['correo_electronico'] ?? '';
            
            // Insertar solo los campos que existen
            $id = \DB::table('propietario')->insertGetId($data);
            
            // Obtener el propietario creado
            $propietario = \DB::table('propietario')->where('id_propietario', $id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Propietario creado correctamente',
                'propietario' => $propietario,
                'columnas_disponibles' => $columnNames
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el propietario: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    $propietario = Propietario::with([
        'mascotas'
    ])->findOrFail($id);

    foreach ($propietario->mascotas as $mascota) {
        $mascota->total_citas = $mascota->citas()->count();
    }

    return response()->json($propietario);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, string $id)
{
    try {

        $validated = $request->validate(
            [
                'nombre' => [
                    'required',
                    'string',
                    'max:30',
                    'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/'
                ],

                'telefono' => [
                    'required',
                    'regex:/^[0-9]{10}$/'
                ],

                'direccion' => [
                    'nullable',
                    'string',
                    'min:10',
                    'max:255'
                ],

                'correo_electronico' => [
                    'nullable',
                    'email',
                    'max:255'
                ],

                'estado' => [
                    'required',
                    'boolean'
                ]
            ],
            [
                'nombre.required' => 'El nombre del propietario es obligatorio.',
                'nombre.max' => 'El nombre no puede exceder 30 caracteres.',
                'nombre.regex' => 'El nombre solo puede contener letras y espacios.',

                'telefono.required' => 'El teléfono es obligatorio.',
                'telefono.regex' => 'El teléfono debe contener exactamente 10 dígitos numéricos.',

                'direccion.min' => 'La dirección debe contener al menos 10 caracteres.',
                'direccion.max' => 'La dirección no puede exceder 255 caracteres.',

                'correo_electronico.email' => 'Ingrese un correo electrónico válido.',
                'correo_electronico.max' => 'El correo electrónico no puede exceder 255 caracteres.',

                'estado.required' => 'Debe seleccionar un estado para el propietario.',
                'estado.boolean' => 'El estado del propietario no es válido.'
            ]
        );

        $validated['estado'] = $request->boolean('estado');

        $propietario = Propietario::findOrFail($id);

        $propietario->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Propietario actualizado correctamente',
            'propietario' => $propietario
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {

        return response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => 'Error al actualizar el propietario',
            'error' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function exportar()
    {
        $propietarios = Propietario::with([
            'mascotas.citas'
        ])
            ->orderBy('nombre')
            ->get();

        $headers = [
            'ID Propietario',
            'Propietario',
            'Telefono',
            'Correo',
            'Direccion',
            'Fecha Registro',
            'ID Mascota',
            'Mascota',
            'Especie',
            'ID Cita',
            'Fecha Cita',
            'Hora Cita',
            'Tipo Servicio',
            'Estado Cita',
        ];

        $rows = [];

        foreach ($propietarios as $propietario) {
            $fechaRegistro = $propietario->fecha_registro
                ? \Carbon\Carbon::parse($propietario->fecha_registro)->format('d/m/Y')
                : 'N/A';

            if ($propietario->mascotas->isEmpty()) {
                $rows[] = [
                    $propietario->id_propietario,
                    $propietario->nombre,
                    $propietario->telefono ?? 'N/A',
                    $propietario->correo_electronico ?? 'N/A',
                    $propietario->direccion ?? 'N/A',
                    $fechaRegistro,
                    'Sin mascotas registradas',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                ];
                continue;
            }

            foreach ($propietario->mascotas as $mascota) {
                $citas = $mascota->citas;

                if ($citas->isEmpty()) {
                    $rows[] = [
                        $propietario->id_propietario,
                        $propietario->nombre,
                        $propietario->telefono ?? 'N/A',
                        $propietario->correo_electronico ?? 'N/A',
                        $propietario->direccion ?? 'N/A',
                        $fechaRegistro,
                        $mascota->id_mascota ?? 'N/A',
                        $mascota->nombre ?? 'N/A',
                        $mascota->especie ?? 'N/A',
                        'Sin citas registradas',
                        '',
                        '',
                        '',
                        '',
                    ];
                    continue;
                }

                foreach ($citas as $cita) {
                    $rows[] = [
                        $propietario->id_propietario,
                        $propietario->nombre,
                        $propietario->telefono ?? 'N/A',
                        $propietario->correo_electronico ?? 'N/A',
                        $propietario->direccion ?? 'N/A',
                        $fechaRegistro,
                        $mascota->id_mascota ?? 'N/A',
                        $mascota->nombre ?? 'N/A',
                        $mascota->especie ?? 'N/A',
                        $cita->id_cita ?? 'N/A',
                        optional($cita->fecha)->format('d/m/Y') ?? 'N/A',
                        $cita->horario ?? 'N/A',
                        $cita->tipo_servicio ?? 'N/A',
                        $cita->estado ?? 'N/A',
                    ];
                }
            }
        }

        $archivoTemporal = $this->crearArchivoXlsx($headers, $rows);
        $nombreArchivo = 'propietarios_' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->download($archivoTemporal, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function crearArchivoXlsx(array $headers, array $rows): string
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('ZipArchive no está disponible en el servidor.');
        }

        $baseTemporal = tempnam(sys_get_temp_dir(), 'propietarios_xlsx_');
        if ($baseTemporal === false) {
            throw new \RuntimeException('No se pudo crear el archivo temporal.');
        }

        $archivoXlsx = $baseTemporal . '.xlsx';
        rename($baseTemporal, $archivoXlsx);

        $zip = new \ZipArchive();
        if ($zip->open($archivoXlsx, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('No se pudo crear el archivo XLSX.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contenidoTypesXml());
        $zip->addFromString('_rels/.rels', $this->contenidoRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->contenidoWorkbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->contenidoWorkbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->contenidoStylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->contenidoSheetXml($headers, $rows));
        $zip->close();

        return $archivoXlsx;
    }

    private function contenidoTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private function contenidoRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function contenidoWorkbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Propietarios" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function contenidoWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function contenidoStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
            . '</styleSheet>';
    }

    private function contenidoSheetXml(array $headers, array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $xml .= '<sheetData>';

        $allRows = array_merge([$headers], $rows);

        foreach ($allRows as $rowIndex => $row) {
            $numeroFila = $rowIndex + 1;
            $xml .= '<row r="' . $numeroFila . '">';

            foreach ($row as $columnIndex => $value) {
                $cellReference = $this->columnaExcel($columnIndex) . $numeroFila;
                $xml .= '<c r="' . $cellReference . '" t="inlineStr"><is><t xml:space="preserve">'
                    . $this->escaparXml((string) $value)
                    . '</t></is></c>';
            }

            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    private function columnaExcel(int $indice): string
    {
        $indice++;
        $columna = '';

        while ($indice > 0) {
            $modulo = ($indice - 1) % 26;
            $columna = chr(65 + $modulo) . $columna;
            $indice = intdiv($indice - 1, 26);
        }

        return $columna;
    }

    private function escaparXml(string $valor): string
    {
        return htmlspecialchars($valor, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }


    public function buscar(Request $request)
{
    $texto = trim($request->get('q', ''));
    $estado = $request->filled('estado') ? $request->boolean('estado') : null;

    $propietarios = Propietario::with('mascotas')
        ->when($estado !== null, function ($query) use ($estado) {
            $query->where('estado', $estado);
        })
        ->when($texto, function ($query) use ($texto) {

            $query->where(function ($subquery) use ($texto) {
                $subquery->where('nombre', 'like', "%{$texto}%")
                    ->orWhereHas('mascotas', function ($q) use ($texto) {
                        $q->where('nombre', 'like', "%{$texto}%");
                    });
            });
        })
        ->orderBy('nombre')
        ->get();

    return response()->json($propietarios);
}
}
