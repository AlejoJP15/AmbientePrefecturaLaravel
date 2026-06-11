<!DOCTYPE html>
<html>
<head><title>Crear Usuario</title></head>
<body>
    <h1>Nuevo Usuario</h1>
    <form method="POST" action="{{ route('usuarios.store') }}">
        @csrf
        <input type="text" name="tipoEntidad" placeholder="Tipo Entidad"><br>
        <input type="text" name="documento" placeholder="Documento"><br>
        <input type="text" name="nombres" placeholder="Nombres"><br>
        <input type="text" name="apellidos" placeholder="Apellidos"><br>
        <input type="email" name="email" placeholder="Email"><br>
        <input type="text" name="telefono" placeholder="Teléfono"><br>
        <input type="text" name="direccion" placeholder="Dirección"><br>
        <button type="submit">Guardar</button>
    </form>
</body>
</html>
