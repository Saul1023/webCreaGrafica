@extends('layouts.app')

@section('title', 'Contacto - CREAGRAFICA')

@section('content')
    <!-- Hero Section -->
    <header class="hero-gradient text-white py-16 md:py-24 text-center">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Contáctanos</h1>
            <p class="text-xl md:text-2xl mb-6">Estamos aquí para ayudarte con tus productos personalizados</p>
            <div class="flex justify-center space-x-4">
                <a href="https://wa.me/59169608947" target="_blank" class="bg-white text-blue-600 px-6 py-3 rounded-full font-bold hover:bg-blue-50 transition-colors duration-300">
                    <i class="fab fa-whatsapp mr-2"></i> Escríbenos por WhatsApp
                </a>
            </div>
        </div>
    </header>

    <!-- Contact Section -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <section id="contacto" class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Contact Info -->
            <div class="bg-white rounded-xl shadow-md p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Información de Contacto</h2>

                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <i class="fas fa-map-marker-alt text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Dirección</h3>
                            <p class="text-gray-600">Zona Villa Victoria</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <i class="fas fa-phone-alt text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Celular</h3>
                            <p class="text-gray-600">+591 69608947</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <i class="fas fa-envelope text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Email</h3>
                            <p class="text-gray-600">info@creagrafica.com</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <i class="fas fa-clock text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Horario de Atención</h3>
                            <p class="text-gray-600">Lunes a Viernes: 8:00 - 18:00</p>
                            <p class="text-gray-600">Sábados: 9:00 - 13:00</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="font-semibold text-gray-800 mb-4">Síguenos en Redes Sociales</h3>
                    <div class="flex space-x-4">
                        <a href="https://www.facebook.com/CreaGraficasubli" target="_blank" class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition-colors duration-300 social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.tiktok.com/@creagrafica0?_t=8liyrDMrYCz&_r=1" target="_blank" class="bg-black text-white p-3 rounded-full hover:bg-gray-800 transition-colors duration-300 social-icon">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="https://wa.me/59169608947" target="_blank" class="bg-green-500 text-white p-3 rounded-full hover:bg-green-600 transition-colors duration-300 social-icon">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="#" class="bg-pink-600 text-white p-3 rounded-full hover:bg-pink-700 transition-colors duration-300 social-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-xl shadow-md p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Envía un Mensaje</h2>

                <!-- Aquí irá el componente Livewire del formulario -->
                @livewire('contact-form')
            </div>
        </section>

        <!-- About Section -->
        <section id="nosotros" class="mt-20 bg-white rounded-xl shadow-md p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Sobre Nosotros</h2>
                <div class="w-20 h-1 bg-blue-600 mx-auto mt-2"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80"
                         alt="Taller de CREAGRAFICA" class="rounded-lg shadow-lg w-full h-auto">
                </div>

                <div>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Nuestra Pasión por la Personalización</h3>
                    <p class="text-gray-600 mb-4">
                        En CREAGRAFICA nos especializamos en crear productos personalizados de alta calidad.
                        Cada taza, cada diseño es creado con atención al detalle y pasión por el arte de la sublimación.
                    </p>
                    <p class="text-gray-600 mb-6">
                        Nuestro equipo de diseñadores trabaja cuidadosamente para ofrecerte diseños únicos que
                        capturen momentos especiales y los transformen en regalos memorables.
                    </p>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <i class="fas fa-check-circle text-blue-600 mb-2"></i>
                            <h4 class="font-semibold">Calidad Premium</h4>
                            <p class="text-sm text-gray-600">Materiales de primera calidad</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <i class="fas fa-bolt text-blue-600 mb-2"></i>
                            <h4 class="font-semibold">Entrega Rápida</h4>
                            <p class="text-sm text-gray-600">Envíos en 24-48 horas</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <i class="fas fa-palette text-blue-600 mb-2"></i>
                            <h4 class="font-semibold">Diseños Exclusivos</h4>
                            <p class="text-sm text-gray-600">Creatividad única</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <i class="fas fa-headset text-blue-600 mb-2"></i>
                            <h4 class="font-semibold">Atención Personalizada</h4>
                            <p class="text-sm text-gray-600">Asesoramiento profesional</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .social-icon {
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            transform: translateY(-5px);
        }
    </style>
@endsection
