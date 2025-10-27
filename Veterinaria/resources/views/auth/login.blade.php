<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ingresar | Clínica Veterinaria</title>

  <!-- Fuente + Tailwind (CDN para prototipo; en prod compila con Vite) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
          colors: {
            vet: {
              50:  '#eefbf7',
              100: '#d8f6ee',
              200: '#b1eadc',
              300: '#84d9c8',
              400: '#59c7b4',
              500: '#2bb39d',   /* principal */
              600: '#1f9382',
              700: '#197568',
              800: '#155d55',
              900: '#0f4640'
            }
          },
          backgroundImage: {
            'paws':
              "radial-gradient(rgba(43,179,157,0.10) 1px, transparent 1px)," +
              "radial-gradient(rgba(43,179,157,0.08) 1px, transparent 1px)",
          }
        }
      }
    }
  </script>
</head>

<body class="m-0 font-sans bg-gradient-to-br from-vet-50 via-white to-vet-100 relative overflow-x-hidden">

  <!-- Patrón sutil de huellitas -->
  <div class="pointer-events-none absolute inset-0 bg-paws bg-[length:22px_22px,44px_44px] bg-[position:0_0,11px_11px]"></div>

  <!-- Blobs decorativos -->
  <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-vet-200/50 blur-3xl"></div>
  <div class="absolute -bottom-24 -right-24 h-80 w-80 rounded-full bg-emerald-200/50 blur-3xl"></div>

  <main class="relative min-h-screen grid place-items-center p-6">
    <section class="grid w-full max-w-6xl overflow-hidden rounded-3xl bg-white/90 shadow-2xl ring-1 ring-vet-100 backdrop-blur md:grid-cols-[1.05fr_1fr]">

      <!-- Panel ilustrado (hero) -->
      <aside class="relative hidden md:flex flex-col justify-between bg-gradient-to-br from-vet-600 to-emerald-700 p-10 text-white">
        <header class="flex items-center gap-3">
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20">
            <!-- Huella -->
            <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor" class="text-white">
              <path d="M17.5 2.5a2.5 2.5 0 1 1-3 3 2.5 2.5 0 0 1 3-3Zm-9 0a2.5 2.5 0 1 1-3 3 2.5 2.5 0 0 1 3-3ZM22 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM6 10a2 2 0 1 1-4-.001A2 2 0 0 1 6 10Zm6.002 1.5c3.1 0 6.498 1.92 6.498 4.86 0 2.04-1.73 3.64-3.87 3.64-1.2 0-2.47-.45-3.13-1.45-.66 1-1.93 1.45-3.13 1.45-2.14 0-3.87-1.6-3.87-3.64 0-2.94 3.4-4.86 6.5-4.86Z"/>
            </svg>
          </span>
          <div>
            <h1 class="text-2xl font-bold tracking-tight">Clínica Veterinaria</h1>
            <p class="text-white/90 text-sm -mt-0.5">Cuidamos a tus mejores amigos</p>
          </div>
        </header>

        <!-- Ilustración SVG -->
        <div class="mx-auto w-full max-w-md">
          <svg viewBox="0 0 400 260" class="w-full drop-shadow-lg">
            <!-- Suelo -->
            <ellipse cx="200" cy="220" rx="150" ry="24" fill="rgba(255,255,255,0.25)" />
            <!-- Perro simplificado -->
            <g transform="translate(65,90)">
              <ellipse cx="90" cy="80" rx="78" ry="46" fill="#FFE6B3"/>
              <circle cx="150" cy="62" r="22" fill="#FFE6B3"/>
              <circle cx="158" cy="56" r="4" fill="#654321"/>
              <path d="M170,58 q8,8 0,16" stroke="#654321" stroke-width="3" fill="none" />
              <rect x="28" y="52" rx="12" ry="12" width="50" height="24" fill="#D97706"/>
              <circle cx="36" cy="64" r="6" fill="#fff"/>
            </g>
            <!-- Gato simplificado -->
            <g transform="translate(210,95)">
              <ellipse cx="70" cy="70" rx="58" ry="38" fill="#D6E8FF"/>
              <circle cx="115" cy="55" r="16" fill="#D6E8FF"/>
              <circle cx="119" cy="53" r="3" fill="#1F2937"/>
              <path d="M128,58 q6,6 0,12" stroke="#1F2937" stroke-width="2.5" fill="none" />
              <path d="M96,32 l10,16 l-16,-10" fill="#93C5FD"/>
            </g>
            <!-- Corazón/latido -->
            <path d="M40,180 h60 l15,-22 l15,38 l18,-30 l12,14 h55"
                  stroke="#fff" stroke-width="6" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>

        <!-- Sellos de confianza -->
        <div class="mt-6 grid grid-cols-3 items-center gap-3 text-xs text-white/90">
          <div class="flex items-center gap-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/15">
              <!-- Escudo -->
              <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor"><path d="M12 2l7 3v6c0 5-3.5 9.5-7 11-3.5-1.5-7-6-7-11V5l7-3z"/></svg>
            </span>
            Cert. NOM-064-ZOO
          </div>
          <div class="flex items-center gap-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/15">
              <!-- Reloj -->
              <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor"><path d="M12 1a11 11 0 1 0 11 11A11.013 11.013 0 0 0 12 1zm1 11h5v2h-7V6h2z"/></svg>
            </span>
            Urgencias 24/7
          </div>
          <div class="flex items-center gap-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/15">
              <!-- Estetoscopio -->
              <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor"><path d="M6 2a1 1 0 0 0-1 1v5a5 5 0 1 0 10 0V3a1 1 0 0 0-2 0v5a3 3 0 1 1-6 0V3a1 1 0 0 0-1-1zM17 12a3 3 0 0 0-3 3 4 4 0 1 0 8 0 3 3 0 0 0-3-3z"/></svg>
            </span>
            Médicos MVZ
          </div>
        </div>
      </aside>
      <div class="p-6 md:p-10 flex flex-col justify-center">
        <header class="mb-6">
          <h2 class="text-2xl font-bold text-slate-900">Bienvenido</h2>
          <p class="text-slate-600 mt-1">Inicia sesión para continuar</p>
        </header>

        {{-- Error global --}}
        @if ($errors->any())
          <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">
            {{ $errors->first() }}
          </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
          @csrf

          <!-- Correo (con icono) -->
          <div>
            <label for="correo_electronico" class="block text-sm font-semibold text-slate-900">Correo electrónico</label>
            <div class="mt-2 relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <!-- Icono correo -->
                <svg viewBox="0 0 24 24" class="h-5 w-5 text-slate-400"><path fill="currentColor" d="M20 4H4a2 2 0 0 0-2 2v.4l10 6.25L22 6.4V6a2 2 0 0 0-2-2Zm0 5.2-8 5-8-5V18a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2Z"/></svg>
              </span>
              <input
                id="correo_electronico" name="correo_electronico" type="email"
                value="{{ old('correo_electronico') }}" required autofocus
                @class([
                  'block w-full rounded-2xl border bg-white pl-10 pr-3 py-2.5 text-slate-900 placeholder:text-slate-400 shadow-sm',
                  'border-slate-300 focus:border-vet-500 ring-2 ring-transparent focus:ring-vet-500 outline-none transition',
                  'border-red-300 focus:border-red-500 focus:ring-red-500' => $errors->has('correo_electronico'),
                ])
                aria-invalid="{{ $errors->has('correo_electronico') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('correo_electronico') ? 'correo-error' : '' }}"
              />
            </div>
            @error('correo_electronico')
              <p id="correo-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
            @enderror
          </div>

          <!-- Password (con icono + mostrar/ocultar) -->
          <div>
            <label for="password" class="block text-sm font-semibold text-slate-900">Contraseña</label>
            <div class="mt-2 relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <!-- Icono candado -->
                <svg viewBox="0 0 24 24" class="h-5 w-5 text-slate-400"><path fill="currentColor" d="M12 1a5 5 0 0 1 5 5v3h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h1V6a5 5 0 0 1 5-5Zm3 8V6a3 3 0 1 0-6 0v3h6Z"/></svg>
              </span>
              <input
                id="password" name="password" :type="showPwd ? 'text' : 'password'" type="password" required
                @class([
                  'block w-full rounded-2xl border bg-white pl-10 pr-11 py-2.5 text-slate-900 placeholder:text-slate-400 shadow-sm',
                  'border-slate-300 focus:border-vet-500 ring-2 ring-transparent focus:ring-vet-500 outline-none transition',
                  'border-red-300 focus:border-red-500 focus:ring-red-500' => $errors->has('password'),
                ])
                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
              />
              <button type="button" aria-label="Mostrar u ocultar contraseña"
                      onclick="const i=document.getElementById('password'); i.type=i.type==='password'?'text':'password'; this.blur();"
                      class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                <!-- Ojo -->
                <svg viewBox="0 0 24 24" class="h-5 w-5"><path fill="currentColor" d="M12 5C7 5 2.73 8.11 1 12c1.73 3.89 6 7 11 7s9.27-3.11 11-7c-1.73-3.89-6-7-11-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z"/></svg>
              </button>
            </div>
            @error('password')
              <p id="password-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
            @enderror
          </div>

          <!-- Opciones -->
          <div class="flex items-center justify-between pt-1">
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
              <input type="checkbox" name="remember" value="1"
                     class="h-4 w-4 rounded border-slate-300 text-vet-600 focus:ring-vet-500" />
              Recuérdame
            </label>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}"
                 class="text-sm font-semibold text-vet-50 bg-vet-600/80 hover:bg-vet-700 text-white px-3 py-1.5 rounded-lg shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-vet-500">
                ¿Olvidaste tu contraseña?
              </a>
            @endif
          </div>

          <!-- Submit -->
          <button type="submit"
                  class="group w-full mt-1 inline-flex items-center justify-center gap-2 rounded-2xl bg-vet-600 px-4 py-3 text-base font-semibold text-white shadow-md hover:bg-vet-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-vet-600 transition">
            <!-- Icono huella -->
            <svg viewBox="0 0 24 24" class="h-5 w-5 opacity-90 group-hover:scale-110 transition" fill="currentColor">
              <path d="M17.5 2.5a2.5 2.5 0 1 1-3 3 2.5 2.5 0 0 1 3-3Zm-9 0a2.5 2.5 0 1 1-3 3 2.5 2.5 0 0 1 3-3ZM22 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM6 10a2 2 0 1 1-4-.001A2 2 0 0 1 6 10Zm6.002 1.5c3.1 0 6.498 1.92 6.498 4.86 0 2.04-1.73 3.64-3.87 3.64-1.2 0-2.47-.45-3.13-1.45-.66 1-1.93 1.45-3.13 1.45-2.14 0-3.87-1.6-3.87-3.64 0-2.94 3.4-4.86 6.5-4.86Z"/>
            </svg>
            Ingresar
          </button>


          <!-- “Beneficios” de la clínica debajo (micro-copy persuasiva) -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-4">
            <div class="rounded-2xl border border-vet-100 bg-vet-50/50 p-3">
              <div class="flex items-center gap-2 text-sm font-semibold text-vet-800">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M21 7 9 19l-6-6 2-2 4 4L19 5z"/></svg>
                Vacunación
              </div>
              <p class="mt-1 text-xs text-slate-600">Esquemas completos y recordatorios.</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-3">
              <div class="flex items-center gap-2 text-sm font-semibold text-emerald-800">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1a5 5 0 0 1 5 5v3h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h1V6a5 5 0 0 1 5-5z"/></svg>
                Cirugía segura
              </div>
              <p class="mt-1 text-xs text-slate-600">Quirófano y monitoreo MVZ.</p>
            </div>
            <div class="rounded-2xl border border-sky-100 bg-sky-50/50 p-3">
              <div class="flex items-center gap-2 text-sm font-semibold text-sky-800">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3l9 5-9 5-9-5 9-5zm0 7l9 5-9 5-9-5 9-5z"/></svg>
                Laboratorio
              </div>
              <p class="mt-1 text-xs text-slate-600">Resultados en horas, no días.</p>
            </div>
          </div>
        </form>
      </div>
    </section>
  </main>

  <!-- Nota para Laravel + Vite:
  @vite('resources/css/app.css')
  -->
</body>
</html>
