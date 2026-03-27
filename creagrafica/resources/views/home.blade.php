@extends('components.layouts.app')

@section('title', 'CREAGRAFICA - Momentos Personalizados')

@section('content')
  <header class="relative bg-gray-900 h-[85vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
      <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
           alt="Fondo Creagrafica"
           class="w-full h-full object-cover opacity-60">
      <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
    </div>

    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
      <span class="inline-block py-1 px-3 rounded-full bg-blue-600/30 border border-blue-400 text-blue-100 text-sm font-semibold tracking-wide mb-4 animate-fade-in-up">
        PERSONALIZACIÓN DE ALTA CALIDAD
      </span>
      <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tight leading-tight">
        Tus Ideas, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">Hechas Realidad</span>
      </h1>
      <p class="text-lg md:text-2xl text-gray-200 mb-10 max-w-2xl mx-auto font-light">
        Transformamos tazas, camisetas y accesorios en regalos únicos llenos de significado y creatividad.
      </p>

      <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
        <a href="#productos" class="group bg-blue-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-blue-700 transition-all shadow-lg hover:shadow-blue-500/50 flex items-center">
          Ver Catálogo
          <i class="fas fa-arrow-down ml-2 group-hover:translate-y-1 transition-transform"></i>
        </a>
        <a href="#nosotros" class="px-8 py-4 rounded-full font-bold text-lg text-white border-2 border-white/30 hover:bg-white/10 transition-all backdrop-blur-sm">
          Conócenos
        </a>
      </div>
    </div>
  </header>

  <main>

    <section id="productos" class="py-24 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Nuestros Productos Destacados</h2>
          <div class="w-24 h-1.5 bg-gradient-to-r from-blue-500 to-purple-600 mx-auto rounded-full"></div>
          <p class="text-gray-600 mt-6 max-w-2xl mx-auto text-lg">
            Explora nuestra colección. Cada pieza es elaborada con materiales premium y técnicas de sublimación de última generación.
          </p>
        </div>

        @livewire('product.list-product')

      </div>
    </section>

    <section class="py-16 bg-blue-900 text-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-10">
            <i class="fas fa-mug-hot absolute top-10 left-10 text-9xl transform -rotate-12"></i>
            <i class="fas fa-tshirt absolute bottom-10 right-10 text-9xl transform rotate-12"></i>
        </div>
        <div class="max-w-7xl mx-auto px-4 relative z-10 flex flex-col md:flex-row items-center justify-between">
            <div class="mb-6 md:mb-0 text-center md:text-left">
                <h3 class="text-3xl font-bold mb-2">¿Tienes un diseño en mente?</h3>
                <p class="text-blue-200">Podemos personalizar cualquier producto con tu logo o imagen.</p>
            </div>
            <a href="https://wa.me/59169608947" target="_blank" class="bg-white text-blue-900 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition shadow-lg flex items-center">
                <i class="fab fa-whatsapp mr-2 text-xl"></i> Cotizar Personalizado
            </a>
        </div>
    </section>

    <section id="nosotros" class="py-24 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-16">

          <div class="lg:w-1/2 relative">
            <div class="absolute -top-4 -left-4 w-24 h-24 bg-blue-100 rounded-full z-0"></div>
            <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-purple-100 rounded-full z-0"></div>
            <img src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
                 alt="Taller Creagrafica"
                 class="relative z-10 rounded-2xl shadow-2xl w-full object-cover h-[400px] lg:h-[500px]">

            <div class="absolute -bottom-8 left-8 z-20 bg-white p-6 rounded-xl shadow-xl border-l-4 border-blue-600 hidden md:block">
                <p class="text-4xl font-bold text-gray-800">100%</p>
                <p class="text-sm text-gray-500 font-medium">Calidad Garantizada</p>
            </div>
          </div>

          <div class="lg:w-1/2">
            <span class="text-blue-600 font-bold tracking-wider uppercase text-sm">Sobre Nosotros</span>
            <h2 class="text-4xl font-bold text-gray-900 mt-2 mb-6">Pasión por el Detalle y la Creatividad</h2>

            <p class="text-gray-600 mb-6 text-lg leading-relaxed">
              En <span class="font-bold text-gray-800">CREAGRAFICA</span>, no solo imprimimos productos, creamos recuerdos. Nos especializamos en el arte de la sublimación, asegurando que cada taza, camiseta o accesorio tenga colores vibrantes y duraderos.
            </p>

            <p class="text-gray-600 mb-8 text-lg leading-relaxed">
              Nuestro equipo de diseñadores trabaja mano a mano contigo para transformar tus fotos y logotipos en regalos que emocionan.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div class="flex items-start">
                <div class="bg-blue-50 p-3 rounded-lg text-blue-600 mr-4">
                  <i class="fas fa-award text-xl"></i>
                </div>
                <div>
                  <h4 class="font-bold text-gray-900">Calidad Premium</h4>
                  <p class="text-sm text-gray-500">Materiales seleccionados para durar.</p>
                </div>
              </div>
              <div class="flex items-start">
                <div class="bg-purple-50 p-3 rounded-lg text-purple-600 mr-4">
                  <i class="fas fa-paint-brush text-xl"></i>
                </div>
                <div>
                  <h4 class="font-bold text-gray-900">Diseños Únicos</h4>
                  <p class="text-sm text-gray-500">Creatividad sin límites para ti.</p>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
  </main>
@endsection
