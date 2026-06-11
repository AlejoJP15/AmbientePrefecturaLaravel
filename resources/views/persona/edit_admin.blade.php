@extends('layouts.app')

@section('content')
@php
    // ¿Tiene fila en usuario_externo?
    $tieneUsuarioExterno = !empty($externo);

    // ¿Es jurídica por tipo_persona? (tolerante a acentos/casos/espacios)
    $tipoRaw = trim((string) ($persona->tipo_persona ?? ''));
    $sinAcentos = strtr($tipoRaw, [
        'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U',
        'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u',
    ]);
    $tipoNorm = mb_strtolower($sinAcentos, 'UTF-8');
    $esJuridicaVista = (strpos($tipoNorm, 'juridic') !== false) || $tieneUsuarioExterno;
@endphp
<link rel="stylesheet" href="{{ asset('styles/edit_user.css') }}">
<link rel="stylesheet" href="{{ asset('styles/menu_general.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Montserrat:wght@600&display=swap" rel="stylesheet">

<div class="main-container">

    {{-- Menú lateral --}}
    @include('menu_general.menu_general')

    {{-- Contenido principal --}}
    <div class="contenido-principal">
        <div class="content-area">

            <div class="usuarios-wrapper">

                <h1>Editar Persona</h1>
                
                <div class="volver-menu">
                    <a href="{{ route('personas.index') }}" class="back-link">Volver al listado</a>
                </div>

                <form action="{{ route('admin.personas.update', ['id' => $persona->id_persona]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <table>
                        {{-- ===== NATURAL ===== --}}
                        <tbody id="bloque-natural" style="{{ $esJuridicaVista ? 'display:none;' : '' }}">
                            <tr>
                            <td><label>Usuario:</label></td>
                            <td>
                                <input type="text" value="{{ $persona->usuario }}" readonly>
                                <input type="hidden" name="usuario" value="{{ $persona->usuario }}">
                            </td>
                            </tr>
                            <tr>
                            <td><label>Nombres:</label></td>
                            <td><input type="text" name="nombres" value="{{ old('nombres', $persona->nombres) }}" {{ $esJuridicaVista ? 'disabled' : 'required' }}></td>
                            </tr>
                            <tr>
                            <td><label>Apellidos:</label></td>
                            <td><input type="text" name="apellidos" value="{{ old('apellidos', $persona->apellidos) }}" {{ $esJuridicaVista ? 'disabled' : 'required' }}></td>
                            </tr>
                            <tr>
                            <td><label>Email:</label></td>
                            <td><input type="email" name="email" value="{{ old('email', $persona->email) }}" {{ $esJuridicaVista ? 'disabled' : 'required' }}></td>
                            </tr>
                            <tr>
                            <td><label>Teléfono:</label></td>
                            <td><input type="text" name="telefono" value="{{ old('telefono', $persona->telefono) }}" {{ $esJuridicaVista ? 'disabled' : '' }}></td>
                            </tr>
                            <tr>
                            <td><label>Celular:</label></td>
                            <td><input type="text" name="celular" value="{{ old('celular', $persona->celular) }}" {{ $esJuridicaVista ? 'disabled' : '' }}></td>
                            </tr>
                            <tr>
                            <td><label>Dirección:</label></td>
                            <td><input type="text" name="direccion" value="{{ old('direccion', $persona->direccion) }}" {{ $esJuridicaVista ? 'disabled' : '' }}></td>
                            </tr>
                        </tbody>

                        {{-- ===== JURÍDICA ===== --}}
                        <tbody id="bloque-juridica" style="{{ $esJuridicaVista ? '' : 'display:none;' }}">
                            <tr>
                            <td><label>Usuario:</label></td>
                            <td>
                                <input type="text" value="{{ $persona->usuario }}" readonly>
                                <input type="hidden" name="usuario" value="{{ $persona->usuario }}">
                            </td>
                            </tr>
                            <tr>
                            <td><label>Nombre organización:</label></td>
                            <td>
                                <input type="text" name="organizacion" value="{{ old('organizacion', optional($externo)->organizacion) }}" {{ $esJuridicaVista ? 'required' : 'disabled' }}>
                                @error('organizacion') <small class="error">{{ $message }}</small> @enderror
                            </td>
                            </tr>
                            <tr>
                            <td><label>Nombre comercial:</label></td>
                            <td>
                                <input type="text" name="comercial" value="{{ old('comercial', optional($externo)->comercial) }}" {{ $esJuridicaVista ? '' : 'disabled' }}>
                                @error('comercial') <small class="error">{{ $message }}</small> @enderror
                            </td>
                            </tr>
                            <tr>
                            <td><label>Cédula/RUC representante:</label></td>
                            <td>
                                <input type="text" name="representante" maxlength="13" value="{{ old('representante', optional($externo)->representante) }}" {{ $esJuridicaVista ? '' : 'disabled' }}>
                                @error('representante') <small class="error">{{ $message }}</small> @enderror
                            </td>
                            </tr>
                            <tr>
                            <td><label>Nombres del representante:</label></td>
                            <td>
                                <input type="text" name="rep_nombres"
                                    value="{{ old('rep_nombres', optional($externo)->nombre_representante ? explode(' ', optional($externo)->nombre_representante, 2)[0] : '') }}"
                                    {{ $esJuridicaVista ? 'required' : 'disabled' }}>
                            </td>
                            </tr>
                            <tr>
                            <td><label>Cargo del representante:</label></td>
                                <td>
                                    <input type="text" name="cargo_representante"
                                        value="{{ old('cargo_representante', optional($externo)->cargo_representante) }}"
                                        {{ $esJuridicaVista ? '' : 'disabled' }}>
                                    @error('cargo_representante') <small class="error">{{ $message }}</small> @enderror
                                </td>
                            </tr>

                            {{-- Contacto (PERSONA) visible también para jurídica --}}
                            <tr>
                            <td><label>Teléfono:</label></td>
                            <td><input type="text" name="telefono" value="{{ old('telefono', $persona->telefono) }}" {{ $esJuridicaVista ? '' : 'disabled' }}></td>
                            </tr>
                            <tr>
                            <td><label>Celular:</label></td>
                            <td><input type="text" name="celular" value="{{ old('celular', $persona->celular) }}" {{ $esJuridicaVista ? '' : 'disabled' }}></td>
                            </tr>
                            <tr>
                            <td><label>Correo electrónico:</label></td>
                            <td><input type="email" name="email" value="{{ old('email', $persona->email) }}" {{ $esJuridicaVista ? 'required' : 'disabled' }}></td>
                            </tr>
                            <tr>
                            <td><label>Dirección:</label></td>
                            <td><input type="text" name="direccion" value="{{ old('direccion', $persona->direccion) }}" {{ $esJuridicaVista ? '' : 'disabled' }}></td>
                            </tr>
                        </tbody>

                        {{-- Rol y Estado (SIEMPRE visibles / editables) --}}
                        <tbody>
                            <tr>
                            <td><label>Rol:</label></td>
                            <td>
                                <select name="id_perfil" required>
                                <option value="">Seleccione un perfil</option>
                                @foreach($perfiles as $item)
                                    <option value="{{ $item->id_perfil }}" {{ (old('id_perfil', $persona->id_perfil) == $item->id_perfil) ? 'selected' : '' }}>
                                    {{ $item->descripcion }}
                                    </option>
                                @endforeach
                                </select>
                            </td>
                            </tr>
                            <tr>
                            <td><label>Estado:</label></td>
                            <td>
                                <select name="id_estado_persona" required>
                                <option value="">Seleccione un estado</option>
                                @foreach($estados as $item)
                                    <option value="{{ $item->id_estado_persona }}" {{ (old('id_estado_persona', $persona->id_estado_persona) == $item->id_estado_persona) ? 'selected' : '' }}>
                                    {{ $item->descripcion }}
                                    </option>
                                @endforeach
                                </select>
                            </td>
                            </tr>
                            
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="2">
                                <div class="botones-form">
                                    <button type="submit">Actualizar</button>
                                    <button type="button" class="btn-cancelar" onclick="window.location.href='{{ route('personas.index') }}'">
                                    Cancelar
                                    </button>
                                </div>
                                </td>
                            </tr>
                        </tfoot>

                    </table>

                    

                </form>

            </div> {{-- /.usuarios-wrapper --}}

        </div> {{-- /.content-area --}}
    </div> {{-- /.contenido-principal --}}
</div> {{-- /.main-container --}}


@endsection
