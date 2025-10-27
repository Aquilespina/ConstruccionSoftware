<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Panel Administrador</title><style>body{font-family:Inter,system-ui,sans-serif;background:#f8fafc;margin:0}header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;background:#14532d;color:#fff}main{max-width:960px;margin:24px auto;padding:0 16px}a.btn{background:#dc2626;color:#fff;padding:8px 12px;border-radius:8px;text-decoration:none}a.btn:hover{background:#b91c1c}.card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px}</style></head>
<body>
<header>
  <div>🐾 Administración</div>
  <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn">Salir</button></form>
</header>
<main>
  <div class="card">
    <h2>Bienvenido, {{ auth()->user()->nombre_usuario }}</h2>
    <p>Este es el panel del administrador.</p>
  </div>
</main>
</body></html>
