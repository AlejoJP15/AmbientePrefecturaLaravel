<?php

namespace App\Http\Controllers;

use App\Models\PlantillaFormato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PlantillaFormatoController extends Controller
{
    /**
     * Muestra el listado de formatos
     */
    public function index()
    {
        $formatos = PlantillaFormato::all();
        return view('formatos.index', compact('formatos'));
    }

    /**
     * Muestra el formulario de edición para Informe Aprobación
     */
    public function editInformeAprobacion()
    {
        $plantilla = PlantillaFormato::firstOrCreate(
            ['tipo_documento' => 'informe_aprobacion'],
            [
                'descripcion' => 'Informe aprobación',
                'url_plantilla' => '/formatos/aprobacion'
            ]
        );

        return view('formatos.aprobacion', compact('plantilla'));
    }

    /**
     * Muestra el formulario de edición para Informe Observado
     */
    public function editInformeObservado()
    {
        $plantilla = PlantillaFormato::firstOrCreate(
            ['tipo_documento' => 'informe_observado'],
            [
                'descripcion' => 'Informe observado',
                'url_plantilla' => '/formatos/observado'
            ]
        );

        return view('formatos.observado', compact('plantilla'));
    }

    /**
     * Muestra el formulario de edición para Informe Pronunciamiento Favorable
     */
    public function editInformePronunciamiento()
    {
        $plantilla = PlantillaFormato::firstOrCreate(
            ['tipo_documento' => 'informe_pronunciamiento'],
            [
                'descripcion' => 'Informe pronunciamiento favorable',
                'url_plantilla' => '/formatos/infpronunciamiento'
            ]
        );

        return view('formatos.infpronunciamiento', compact('plantilla'));
    }

    /**
     * Muestra el formulario de edición para Memo Aprobación
     */
    public function editMemoAprobacion()
    {
        $plantilla = PlantillaFormato::firstOrCreate(
            ['tipo_documento' => 'memo_aprobacion'],
            [
                'descripcion' => 'Memo aprobación',
                'url_plantilla' => '/formatos/memoaprobacion'
            ]
        );

        return view('formatos.memoaprobacion', compact('plantilla'));
    }

    /**
     * Muestra el formulario de edición para Memo Observado
     */
    public function editMemoObservado()
    {
        $plantilla = PlantillaFormato::firstOrCreate(
            ['tipo_documento' => 'memo_observado'],
            [
                'descripcion' => 'Memo observado',
                'url_plantilla' => '/formatos/memoobservado'
            ]
        );

        return view('formatos.memoobservado', compact('plantilla'));
    }

    /**
     * Muestra el formulario de edición para Memo Pronunciamiento
     */
    public function editMemoPronunciamiento()
    {
        $plantilla = PlantillaFormato::firstOrCreate(
            ['tipo_documento' => 'memo_pronunciamiento'],
            [
                'descripcion' => 'Memo pronunciamiento favorable',
                'url_plantilla' => '/formatos/memopronunciamiento'
            ]
        );

        return view('formatos.memopronunciamiento', compact('plantilla'));
    }

    /**
     * Muestra el formulario de edición para Oficio Aprobación
     */
    public function editOficioAprobacion()
    {
        $plantilla = PlantillaFormato::firstOrCreate(
            ['tipo_documento' => 'oficio_aprobacion'],
            [
                'descripcion' => 'Oficio aprobación',
                'url_plantilla' => '/formatos/ofiaprobacion'
            ]
        );

        return view('formatos.ofiaprobacion', compact('plantilla'));
    }

    /**
     * Muestra el formulario de edición para Oficio Observado
     */
    public function editOficioObservado()
    {
        $plantilla = PlantillaFormato::firstOrCreate(
            ['tipo_documento' => 'oficio_observado'],
            [
                'descripcion' => 'Oficio observado',
                'url_plantilla' => '/formatos/ofiobservado'
            ]
        );

        return view('formatos.ofiobservado', compact('plantilla'));
    }

    /**
     * Muestra el formulario de edición para Oficio Pronunciamiento
     */
    public function editOficioPronunciamiento()
    {
        $plantilla = PlantillaFormato::firstOrCreate(
            ['tipo_documento' => 'oficio_pronunciamiento'],
            [
                'descripcion' => 'Oficio pronunciamiento favorable',
                'url_plantilla' => '/formatos/ofipronunciamiento'
            ]
        );

        return view('formatos.ofipronunciamiento', compact('plantilla'));
    }

    /**
     * Muestra el formulario de edición para Resolución
     */
    public function editResolucion()
    {
        $plantilla = PlantillaFormato::firstOrCreate(
            ['tipo_documento' => 'resolucion'],
            [
                'descripcion' => 'Resolución',
                'url_plantilla' => '/formatos/resolucion'
            ]
        );

        return view('formatos.resolucion', compact('plantilla'));
    }

    /**
     * Guarda o actualiza la plantilla
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'asunto' => 'nullable|string',
                'para' => 'nullable|string',
                'cuerpo' => 'nullable|string',
                'antecedentes' => 'nullable|string',
                'analisis' => 'nullable|string',
                'objetivos' => 'nullable|string',
                'observaciones' => 'nullable|string',
                'detalle' => 'nullable|string',
                'conclusiones' => 'nullable|string',
            ]);

            $plantilla = PlantillaFormato::findOrFail($id);
            
            // Preparar datos para actualizar (solo los que no sean null)
            $dataToUpdate = [];
            
            if ($request->has('asunto')) $dataToUpdate['asunto'] = $request->input('asunto');
            if ($request->has('para')) $dataToUpdate['para'] = $request->input('para');
            if ($request->has('cuerpo')) $dataToUpdate['cuerpo'] = $request->input('cuerpo');
            if ($request->has('antecedentes')) $dataToUpdate['antecedentes'] = $request->input('antecedentes');
            if ($request->has('analisis')) $dataToUpdate['analisis'] = $request->input('analisis');
            if ($request->has('objetivos')) $dataToUpdate['objetivos'] = $request->input('objetivos');
            if ($request->has('observaciones')) $dataToUpdate['observaciones'] = $request->input('observaciones');
            if ($request->has('detalle')) $dataToUpdate['detalle'] = $request->input('detalle');
            if ($request->has('conclusiones')) $dataToUpdate['conclusiones'] = $request->input('conclusiones');

            $plantilla->update($dataToUpdate);

            return response()->json([
                'success' => true,
                'message' => 'Plantilla guardada correctamente',
                'data' => $plantilla
            ]);

        } catch (\Exception $e) {
            Log::error('Error al guardar plantilla: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la plantilla: ' . $e->getMessage()
            ], 500);
        }
    }
}