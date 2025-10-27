<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Panel Recepción</title><style>body{font-family:Inter,system-ui,sans-serif;background:#f8fafc;margin:0}header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;background:#166534;color:#fff}main{max-width:1100px;margin:24px auto;padding:0 16px}a.btn{background:#166534;color:#fff;padding:8px 12px;border-radius:8px;text-decoration:none}a.btn:hover{background:#14532d}.btn.red{background:#dc2626}.btn.red:hover{background:#b91c1c}.card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:16px}.grid{display:grid;grid-template-columns:1fr;gap:16px}@media(min-width:900px){.grid{grid-template-columns:1fr 1fr}}.actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:12px}</style></head>
<body>
<header>
  <div>🐾 Recepción</div>
  <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn red">Salir</button></form>
</header>
<main>
  <div class="card">
    <h2>Hola, {{ auth()->user()->nombre_usuario }}</h2>
    <p>Este es el panel de recepción.</p>
  </div>
  <div class="grid">
    <div class="card">
      <h3>Gestión de Propietarios</h3>
      <ul>
        <li>Registro de nuevos propietarios</li>
        <li>Modificación de datos de propietarios</li>
        <li>Consulta y búsqueda de propietarios</li>
      </ul>
      <div class="actions">
        <a href="{{ route('propietarios.create') }}" class="btn">Registrar propietario</a>
        <a href="{{ route('propietarios.index') }}" class="btn">Modificar propietario</a>
        <a href="{{ route('propietarios.search') }}" class="btn">Buscar propietario</a>
      </div>
    </div>
    <div class="card">
      <h3>Gestión de Mascotas</h3>
      <ul>
        <li>Registro de nuevas mascotas</li>
        <li>Modificación de datos de mascotas</li>
        <li>Historial médico básico</li>
      </ul>
      <div class="actions">
        <a href="{{ route('mascotas.create') }}" class="btn">Registrar mascota</a>
        <a href="{{ route('mascotas.index') }}" class="btn">Modificar mascota</a>
        <a href="{{ route('mascotas.index') }}" class="btn">Ver historial</a>
      </div>
    </div>
  </div>
</main>
</body></html>
