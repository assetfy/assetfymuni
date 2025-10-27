<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AssetFy - Gestión Integral de Activos</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/assetfy-logo.png') }}" alt="AssetFy Logo" class="logo-img">
                    </a>
                </div>
                <nav class="nav">
                    <a href="#quienes-somos" class="nav-link">Quiénes somos</a>
                    <a href="#soluciones" class="nav-link">Soluciones</a>
                    <a href="#demo" class="nav-link nav-link-demo">Solicita una Demo</a>
                    <a href="{{ route('login') }}" class="nav-link nav-link-login">Ingreso Clientes</a>
                </nav>
                <div class="mobile-menu-toggle" id="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobile-menu">
        <div class="mobile-menu-close" id="mobile-menu-close">
            <i class="fas fa-times"></i>
        </div>
        <nav class="mobile-nav">
            <a href="#quienes-somos" class="mobile-nav-link">Quiénes somos</a>
            <a href="#soluciones" class="mobile-nav-link">Soluciones</a>
            <a href="#demo" class="mobile-nav-link">Solicita una Demo</a>
            <a href="{{ route('login') }}" class="mobile-nav-link">Ingreso Clientes</a>
        </nav>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">Gestiona de forma automatizada tus bienes, Clientes y Servicios, desde una única plataforma</h1>
                    <p class="hero-subtitle">Logra mejorar tu rentabilidad mediante la digitalización de los procesos de mantenimientos correctivos y preventivos, gestion de proveedores, Clientes y tecnicos, con una solución que crece junto a tu negocio.</p>
                    <div class="hero-buttons">
                        <a href="#demo" class="btn btn-primary">Solicitar Demo</a>
                        <a href="#soluciones" class="btn btn-secondary">Conocer más</a>
                    </div>
                </div>
                <div class="hero-carousel">
                    <div class="carousel-container">
                        <div class="carousel-track" id="carousel-track">
                            <!-- Carousel Item 1 -->
                            <div class="carousel-item active">
                                <div class="carousel-placeholder">
                                    <!-- TODO: Upload image to public/images/carousel/slide1.jpg -->
                                    <!-- Recommended size: 800x600px -->
                                    <img src="{{ asset('images/carousel/slide1.jpg') }}" alt="Gestión de Inventario" onerror="this.parentElement.innerHTML='<div class=\'placeholder-icon\'><i class=\'fas fa-warehouse fa-5x\'></i><p>Gestión de Inventario</p></div>'">
                                </div>
                            </div>
                            <!-- Carousel Item 2 -->
                            <div class="carousel-item">
                                <div class="carousel-placeholder">
                                    <!-- TODO: Upload image to public/images/carousel/slide2.jpg -->
                                    <!-- Recommended size: 800x600px -->
                                    <img src="{{ asset('images/carousel/slide2.jpg') }}" alt="Control de Proveedores" onerror="this.parentElement.innerHTML='<div class=\'placeholder-icon\'><i class=\'fas fa-truck fa-5x\'></i><p>Control de Proveedores</p></div>'">
                                </div>
                            </div>
                            <!-- Carousel Item 3 -->
                            <div class="carousel-item">
                                <div class="carousel-placeholder">
                                    <!-- TODO: Upload image to public/images/carousel/slide3.jpg -->
                                    <!-- Recommended size: 800x600px -->
                                    <img src="{{ asset('images/carousel/slide3.jpg') }}" alt="Trazabilidad de Fabricación" onerror="this.parentElement.innerHTML='<div class=\'placeholder-icon\'><i class=\'fas fa-industry fa-5x\'></i><p>Trazabilidad de Fabricación</p></div>'">
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control prev" id="carousel-prev">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="carousel-control next" id="carousel-next">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <div class="carousel-indicators" id="carousel-indicators">
                            <span class="indicator active" data-slide="0"></span>
                            <span class="indicator" data-slide="1"></span>
                            <span class="indicator" data-slide="2"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quiénes Somos Section -->
    <section class="quienes-somos" id="quienes-somos">
        <div class="container">
            <h2 class="section-title">Quiénes Somos</h2>
            <div class="quienes-somos-content">
                <p class="section-description">AssetFy es la plataforma líder en gestión integral de activos empresariales. Ayudamos a las empresas a optimizar sus operaciones mediante soluciones tecnológicas innovadoras que facilitan el control, mantenimiento y trazabilidad de todos sus activos.</p>
                <p class="section-description">Con años de experiencia en el sector, entendemos los desafíos únicos que enfrentan las organizaciones modernas y ofrecemos herramientas diseñadas específicamente para superarlos.</p>
            </div>
        </div>
    </section>

    <!-- Soluciones Section -->
    <section class="soluciones" id="soluciones">
        <div class="container">
            <h2 class="section-title">Nuestras Soluciones</h2>
            <p class="section-subtitle">Descubre cómo AssetFy puede transformar la gestión de tus activos</p>
            
            <div class="solutions-grid">
                <!-- Solution 1: Inventory -->
                <div class="solution-card">
                    <div class="solution-logo">
                        <img src="{{ asset('images/inventory-logo.png') }}" alt="Inventory Logo">
                    </div>
                    <h3 class="solution-title">Inventory</h3>
                    <p class="solution-description">Centraliza la gestión de tus bienes e Inmuebles con control total:.</p>
                    <ul class="solution-features">
                        <li><i class="fas fa-check"></i> Gestión de Inventarios relacionado con Ubicaciones / Responsables y Usuarios</li>
                        <li><i class="fas fa-check"></i> Gestión de Tareas</li>
                        <li><i class="fas fa-check"></i> Planes de Mantenimiento</li>
                        <li><i class="fas fa-check"></i>Gestión de Proveedores</li>
          <li><i class="fas fa-check"></i>Y mas funcionalidades por descubrir!</li>
                    </ul>
                    <a href="#demo" class="solution-btn">Conocer más</a>
                </div>

                <!-- Solution 2: Supplier -->
                <div class="solution-card">
                    <div class="solution-logo">
                        <img src="{{ asset('images/supplier-logo.png') }}" alt="Supplier Logo">
                    </div>
                    <h3 class="solution-title">Supplier</h3>
                    <p class="solution-description">Automatizá tus operaciones y tareas de campo..</p>
                    <ul class="solution-features">
                        <li><i class="fas fa-check"></i> Gestiona tu lista de clientes</li>
                        <li><i class="fas fa-check"></i> Recibe y crea solicitudes de tus clientes</li>
                        <li><i class="fas fa-check"></i> Gestiona la asignación de servicios a tus tecnicos</li>
                        <li><i class="fas fa-check"></i> Listas de Precios</li>
                         <li><i class="fas fa-check"></i>Tareas y Equipos de Trabajo</li>
                    </ul>
                    <a href="#demo" class="solution-btn">Conocer más</a>
                </div>

                <!-- Solution 3: Manufacturer -->
                <div class="solution-card">
                    <div class="solution-logo">
                        <img src="{{ asset('images/manufactorer-logo.png') }}" alt="Manufacturer Logo">
                    </div>
                    <h3 class="solution-title">Manufacturer</h3>
                    <p class="solution-description">Digitalizá tu catálogo y gestioná garantías inteligentes..</p>
                    <ul class="solution-features">
                        <li><i class="fas fa-check"></i> Gestiona multiples marcas</li>
                        <li><i class="fas fa-check"></i> Administration los atributos de tus productos</li>
                        <li><i class="fas fa-check"></i> Gestiona a tus representantes oficiales</li>
                        <li><i class="fas fa-check"></i> Gestiona la activación y registro de garantias</li>
                    </ul>
                    <a href="#demo" class="solution-btn">Conocer más</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Cómo se Integran Section -->
    <section class="integracion">
        <div class="container">
            <h2 class="section-title">Cómo se Integran Nuestras Soluciones</h2>
            <p class="section-subtitle">Un ecosistema completo para la gestión de tus activos</p>
            
            <div class="integracion-content">
                <div class="integracion-diagram">
                    <div class="diagram-placeholder">
                        <img src="{{ asset('images/integration-diagram.jpg') }}" alt="Diagrama de Integración">
                    </div>
                </div>
                
                <div class="integracion-items">
                    <div class="integracion-item">
                        <div class="item-number">1</div>
                        <div class="item-content">
                            <h4>Gestión de Cotizaciones y Ordenes de Trabajo</h4>
                            <p>Todos los módulos se sincronizan automáticamente, asegurando que la información esté siempre actualizada en toda la plataforma.</p>
                        </div>
                    </div>
                    
                    <div class="integracion-item">
                        <div class="item-number">2</div>
                        <div class="item-content">
                            <h4>Flujo de Trabajo Unificado</h4>
                            <p>Desde la solicitud de materiales hasta la entrega del producto final, todo se gestiona en un solo sistema integrado.</p>
                        </div>
                    </div>
                    
                    <div class="integracion-item">
                        <div class="item-number">3</div>
                        <div class="item-content">
                            <h4>Visibilidad Total</h4>
                            <p>Obtén una vista 360° de todas tus operaciones con dashboards intuitivos y reportes personalizables.</p>
                        </div>
                    </div>
                    
                    <div class="integracion-item">
                        <div class="item-number">4</div>
                        <div class="item-content">
                            <h4>Escalabilidad</h4>
                            <p>Nuestra plataforma crece contigo. Añade módulos y funcionalidades según tus necesidades evolucionen.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action / Demo Section -->
    <section class="cta" id="demo">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">¿Listo para Transformar tu Gestión de Activos?</h2>
                <p class="cta-description">Solicita una demo personalizada y descubre cómo AssetFy puede ayudarte a optimizar tus operaciones</p>
                <div class="cta-buttons">
                    <a href="mailto:info@assetfy.com?subject=Solicitud de Demo" class="btn btn-primary btn-large">Solicitar Demo Gratis</a>
                    <a href="{{ route('login') }}" class="btn btn-secondary btn-large">Acceder a mi Cuenta</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>AssetFy</h4>
                    <p>Gestión Integral de Activos Empresariales</p>
                </div>
                <div class="footer-section">
                    <h4>Contacto</h4>
                    <p>Email: info@assetfy.com</p>
                    <p>Teléfono: +54 11 XXXX-XXXX</p>
                </div>
                <div class="footer-section">
                    <h4>Síguenos</h4>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} AssetFy. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Carousel Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Carousel functionality
            const track = document.getElementById('carousel-track');
            const items = document.querySelectorAll('.carousel-item');
            const indicators = document.querySelectorAll('.indicator');
            const prevBtn = document.getElementById('carousel-prev');
            const nextBtn = document.getElementById('carousel-next');
            let currentSlide = 0;
            let autoplayInterval;

            function goToSlide(index) {
                items[currentSlide].classList.remove('active');
                indicators[currentSlide].classList.remove('active');
                
                currentSlide = index;
                if (currentSlide < 0) currentSlide = items.length - 1;
                if (currentSlide >= items.length) currentSlide = 0;
                
                items[currentSlide].classList.add('active');
                indicators[currentSlide].classList.add('active');
            }

            function nextSlide() {
                goToSlide(currentSlide + 1);
            }

            function prevSlide() {
                goToSlide(currentSlide - 1);
            }

            // Autoplay every 3 seconds
            function startAutoplay() {
                autoplayInterval = setInterval(nextSlide, 3000);
            }

            function stopAutoplay() {
                clearInterval(autoplayInterval);
            }

            // Event listeners
            nextBtn.addEventListener('click', function() {
                stopAutoplay();
                nextSlide();
                startAutoplay();
            });

            prevBtn.addEventListener('click', function() {
                stopAutoplay();
                prevSlide();
                startAutoplay();
            });

            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', function() {
                    stopAutoplay();
                    goToSlide(index);
                    startAutoplay();
                });
            });

            // Start autoplay on page load
            startAutoplay();

            // Pause autoplay when tab is not visible
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopAutoplay();
                } else {
                    startAutoplay();
                }
            });

            // Mobile menu functionality
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuClose = document.getElementById('mobile-menu-close');
            const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

            mobileMenuToggle.addEventListener('click', function() {
                mobileMenu.classList.add('active');
                document.body.style.overflow = 'hidden';
            });

            mobileMenuClose.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            });

            mobileNavLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.remove('active');
                    document.body.style.overflow = '';
                });
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href !== '#' && document.querySelector(href)) {
                        e.preventDefault();
                        document.querySelector(href).scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
