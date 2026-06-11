<!DOCTYPE html>
<html>
<head><title>Editar Usuario</title></head>
<body>
    <h1>Editar Usuario</h1>

    <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}">
        @csrf
        @method('PUT')

        <label for="tipoEntidad">Tipo Entidad:</label>
        <input type="text" name="tipoEntidad" value="{{ old('tipoEntidad', $usuario->tipoEntidad) }}"><br>

        <label for="documento">Documento:</label>
        <input type="text" name="documento" value="{{ old('documento', $usuario->documento) }}"><br>

        <label for="nombres">Nombres:</label>
        <input type="text" name="nombres" value="{{ old('nombres', $usuario->nombres) }}"><br>

        <label for="apellidos">Apellidos:</label>
        <input type="text" name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}"><br>

        <label for="email">Email:</label>
        <input type="email" name="email" value="{{ old('email', $usuario->email) }}"><br>

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}"><br>

        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" value="{{ old('direccion', $usuario->direccion) }}"><br>

        <button type="submit">Actualizar</button>
    </form>
</body>
</html>
