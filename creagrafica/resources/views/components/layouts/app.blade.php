<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CREAGRAFICA - Productos Personalizados')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @livewireStyles
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
        }

        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        /* Dropdown menu styles */
        .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
  <nav class="bg-blue-600 shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <a href="{{ url('/') }}" class="text-white text-xl font-bold flex items-center">
            <i class="fas fa-mug-hot mr-2"></i> CREAGRAFICA
          </a>
        </div>

        <div class="hidden md:flex items-center space-x-8">
          <a href="{{ url('/#productos') }}" class="text-white hover:text-blue-200 px-3 py-2 transition-all">Productos</a>
          <a href="{{ url('/#nosotros') }}" class="text-white hover:text-blue-200 px-3 py-2 transition-all">Nosotros</a>
          <a href="{{ url('/#contacto') }}" class="text-white hover:text-blue-200 px-3 py-2 transition-all">Contacto</a>

          @auth
            <div class="relative dropdown">
              <button class="flex items-center text-white hover:text-blue-200 px-3 py-2 transition-all">
                <i class="fas fa-user-circle mr-2"></i>
                <span>{{ Auth::user()->name ?? Auth::user()->nombre_completo }}</span>
                <i class="fas fa-chevron-down ml-2 text-xs"></i>
              </button>

              <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50">
                @if(auth()->user()->isAdmin())
                  <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Panel Admin
                  </a>
                  <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50">
                    <i class="fas fa-users-cog mr-3"></i>
                    Usuarios
                  </a>
                @endif
                <a href="{{ route('products.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50">
                  <i class="fas fa-shopping-bag mr-3"></i>
                  Mis Productos
                </a>
                <div class="border-t border-gray-100 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="flex items-center w-full px-4 py-2 text-red-600 hover:bg-red-50">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    Cerrar Sesión
                  </button>
                </form>
              </div>
            </div>
          @else
            <div class="flex items-center space-x-4">
              <a href="{{ route('login') }}" class="text-white hover:text-blue-200 px-3 py-2 transition-all font-medium">
                <i class="fas fa-sign-in-alt mr-1"></i> Iniciar Sesión
              </a>
              <a href="{{ route('register') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition-all">
                <i class="fas fa-user-plus mr-1"></i> Registrarse
              </a>
            </div>
          @endauth

          <a href="#" class="bg-yellow-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-yellow-600 transition-all">
            <i class="fas fa-shopping-cart mr-2"></i> Carrito
          </a>
        </div>

        <div class="md:hidden flex items-center">
          <button id="menu-btn" class="text-white focus:outline-none">
            <i class="fas fa-bars text-2xl"></i>
          </button>
        </div>
      </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-blue-700 pb-4">
      <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
        <a href="{{ url('/#productos') }}" class="text-white block px-3 py-2 hover:bg-blue-600 rounded-md">Productos</a>
        <a href="{{ url('/#nosotros') }}" class="text-white block px-3 py-2 hover:bg-blue-600 rounded-md">Nosotros</a>
        <a href="{{ url('/#contacto') }}" class="text-white block px-3 py-2 hover:bg-blue-600 rounded-md">Contacto</a>

        @auth
          <div class="pt-4 border-t border-blue-600">
            <p class="text-white px-3 py-2 font-medium">Hola, {{ Auth::user()->name ?? Auth::user()->nombre_completo }}</p>

            @if(auth()->user()->isAdmin())
              <a href="{{ route('admin.dashboard') }}" class="text-white block px-3 py-2 hover:bg-blue-600 rounded-md">
                <i class="fas fa-tachometer-alt mr-2"></i> Panel Admin
              </a>
              <a href="{{ route('admin.users') }}" class="text-white block px-3 py-2 hover:bg-blue-600 rounded-md">
                <i class="fas fa-users-cog mr-2"></i> Usuarios
              </a>
            @endif

            <a href="{{ route('products.index') }}" class="text-white block px-3 py-2 hover:bg-blue-600 rounded-md">
              <i class="fas fa-shopping-bag mr-2"></i> Mis Productos
            </a>

            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left text-white px-3 py-2 hover:bg-blue-600 rounded-md">
                <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
              </button>
            </form>
          </div>
        @else
          <div class="pt-4 border-t border-blue-600">
            <a href="{{ route('login') }}" class="text-white block px-3 py-2 hover:bg-blue-600 rounded-md">
              <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
            </a>
            <a href="{{ route('register') }}" class="bg-white text-blue-600 block px-3 py-2 rounded-md font-medium text-center mt-2">
              <i class="fas fa-user-plus mr-2"></i> Registrarse
            </a>
          </div>
        @endauth

        <a href="#" class="bg-yellow-500 text-white block px-3 py-2 rounded-md font-medium text-center mt-2">
          <i class="fas fa-shopping-cart mr-2"></i> Carrito
        </a>
      </div>
    </div>
  </nav>

  <main class="flex-grow">
      @if(isset($slot))
          {{ $slot }}
      @else
          @yield('content')
      @endif
  </main>

  <footer class="bg-gray-800 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
          <h3 class="text-xl font-bold mb-4 flex items-center">
            <i class="fas fa-mug-hot mr-2"></i> CREAGRAFICA
          </h3>
          <p class="text-gray-400">Productos personalizados hechos con amor y dedicación para momentos especiales.</p>
        </div>
        <div>
          <h4 class="text-lg font-semibold mb-4">Enlaces Rápidos</h4>
          <ul class="space-y-2">
            <li><a href="{{ url('/#productos') }}" class="text-gray-400 hover:text-white transition-all">Productos</a></li>
            <li><a href="{{ url('/#nosotros') }}" class="text-gray-400 hover:text-white transition-all">Nosotros</a></li>
            <li><a href="{{ url('/#contacto') }}" class="text-gray-400 hover:text-white transition-all">Contacto</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white transition-all">Términos y Condiciones</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-lg font-semibold mb-4">Productos</h4>
          <ul class="space-y-2">
            <li><a href="#" class="text-gray-400 hover:text-white transition-all">Tazas Personalizadas</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white transition-all">Polos Sublimados</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white transition-all">Llaveros</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white transition-all">Otros Productos</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-lg font-semibold mb-4">Newsletter</h4>
          <p class="text-gray-400 mb-4">Suscríbete para recibir ofertas especiales y novedades.</p>
          <form class="flex">
            <input type="email" placeholder="Tu correo" class="px-4 py-2 rounded-l-lg focus:outline-none text-gray-800 w-full">
            <button type="submit" class="bg-blue-600 px-4 py-2 rounded-r-lg hover:bg-blue-700 transition-all">
              <i class="fas fa-paper-plane"></i>
            </button>
          </form>
        </div>
      </div>
      <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
        <p>&copy; 2024 CREAGRAFICA. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>

  <a href="https://wa.me/59169608947" target="_blank" class="fixed bottom-6 right-6 bg-green-500 text-white p-4 rounded-full shadow-lg hover:bg-green-600 transition-all z-50">
    <i class="fab fa-whatsapp text-2xl"></i>
  </a>

  <button id="back-to-top" class="fixed bottom-6 left-6 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all hidden z-50">
    <i class="fas fa-arrow-up"></i>
  </button>

  @livewireScripts
  <script>
    // Mobile menu toggle
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuBtn && mobileMenu) {
      menuBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
      });
    }

    // Back to top button
    const backToTopBtn = document.getElementById('back-to-top');

    if (backToTopBtn) {
      window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
          backToTopBtn.classList.remove('hidden');
        } else {
          backToTopBtn.classList.add('hidden');
        }
      });

      backToTopBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }

    // Cerrar menú móvil al hacer clic en un enlace
    document.querySelectorAll('#mobile-menu a').forEach(link => {
      link.addEventListener('click', () => {
        mobileMenu.classList.add('hidden');
      });
    });

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(event) {
      const dropdowns = document.querySelectorAll('.dropdown');
      dropdowns.forEach(dropdown => {
        if (!dropdown.contains(event.target)) {
          const menu = dropdown.querySelector('.dropdown-menu');
          if (menu) {
            menu.style.opacity = '0';
            menu.style.visibility = 'hidden';
            menu.style.transform = 'translateY(-10px)';
          }
        }
      });
    });
  </script>
</body>
</html>
