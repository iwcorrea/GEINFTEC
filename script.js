(function() {
    'use strict';

    // 1. HERO CANVAS
    const canvas = document.getElementById('hero-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let width, height;
        const particles = [];
        const NUM_PARTICLES = 80;
        const CONNECTION_DIST = 150;

        function resizeCanvas() {
            const rect = canvas.parentElement.getBoundingClientRect();
            width = rect.width;
            height = rect.height;
            canvas.width = width;
            canvas.height = height;
        }

        class Particle {
            constructor() { this.reset(); }
            reset() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.vx = (Math.random() - 0.5) * 0.6;
                this.vy = (Math.random() - 0.5) * 0.6;
                this.radius = 1.5 + Math.random() * 2;
            }
            update() {
                this.x += this.vx;
                this.y += this.vy;
                if (this.x < 0 || this.x > width) this.vx *= -1;
                if (this.y < 0 || this.y > height) this.vy *= -1;
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(0,245,212,0.7)';
                ctx.fill();
            }
        }

        function initParticles() {
            particles.length = 0;
            for (let i = 0; i < NUM_PARTICLES; i++) {
                particles.push(new Particle());
            }
        }

        function drawLines() {
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < CONNECTION_DIST) {
                        const alpha = 1 - (dist / CONNECTION_DIST);
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = `rgba(0,245,212,${alpha * 0.4})`;
                        ctx.lineWidth = 0.8;
                        ctx.stroke();
                    }
                }
            }
        }

        function animateCanvas() {
            ctx.clearRect(0, 0, width, height);
            particles.forEach(function(p) { p.update(); p.draw(); });
            drawLines();
            requestAnimationFrame(animateCanvas);
        }

        resizeCanvas();
        initParticles();
        animateCanvas();

        window.addEventListener('resize', function() {
            resizeCanvas();
            initParticles();
        });
    }

    // 2. MÁQUINA DE ESCRIBIR
    const rotatingEl = document.getElementById('rotating-text');
    if (rotatingEl) {
        let phrases = [];
        if (rotatingEl.dataset.phrases) {
            try {
                phrases = JSON.parse(rotatingEl.dataset.phrases);
            } catch(e) {
                phrases = ['Ingeniería de vanguardia', 'Construcción inteligente', 'Software que transforma'];
            }
        } else {
            phrases = ['Ingeniería de vanguardia', 'Construcción inteligente', 'Software que transforma'];
        }

        let phraseIndex = 0, charIndex = 0, isDeleting = false, typeSpeed = 100;

        function typeEffect() {
            const current = phrases[phraseIndex];
            if (!isDeleting) {
                rotatingEl.textContent = current.substring(0, charIndex + 1);
                charIndex++;
                typeSpeed = (charIndex === current.length) ? 2000 : (80 + Math.random() * 40);
                if (charIndex === current.length) isDeleting = true;
            } else {
                rotatingEl.textContent = current.substring(0, charIndex);
                charIndex--;
                if (charIndex < 0) {
                    isDeleting = false;
                    phraseIndex = (phraseIndex + 1) % phrases.length;
                    typeSpeed = 300;
                } else {
                    typeSpeed = 40 + Math.random() * 30;
                }
            }
            setTimeout(typeEffect, typeSpeed);
        }
        setTimeout(typeEffect, 500);
    }

    // 3. MENÚ HAMBURGUESA
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('nav-links');
    if (hamburger && navLinks) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('open');
        });
        navLinks.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                navLinks.classList.remove('open');
            });
        });
    }

    // 4. BARRA DE PROGRESO
    const progressBar = document.getElementById('progress-bar');
    if (progressBar) {
        window.addEventListener('scroll', function() {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            progressBar.style.width = (scrollTop / docHeight * 100) + '%';
        });
    }

    // 5. BOTÓN VOLVER ARRIBA
    const backBtn = document.getElementById('back-to-top');
    if (backBtn) {
        window.addEventListener('scroll', function() {
            backBtn.classList.toggle('visible', window.scrollY > 400);
        });
        backBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // 6. INTERSECTION OBSERVER
    const fadeElements = document.querySelectorAll('.fade-up');
    if (fadeElements.length > 0) {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    const statNumber = entry.target.querySelector('.stat-number');
                    if (statNumber && !statNumber.dataset.animated) {
                        const target = parseInt(statNumber.dataset.count, 10);
                        let current = 0;
                        const increment = Math.ceil(target / 60);
                        const timer = setInterval(function() {
                            current += increment;
                            if (current >= target) {
                                current = target;
                                clearInterval(timer);
                            }
                            statNumber.textContent = current + (target === 100 ? '%' : '');
                        }, 25);
                        statNumber.dataset.animated = 'true';
                    }
                }
            });
        }, { threshold: 0.2 });
        fadeElements.forEach(function(el) { observer.observe(el); });
    }

    // ============================================================
    // 7. FORMULARIO DE CONTACTO (ENVÍO REAL A enviar_mensaje.php)
    // ============================================================
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        const formFeedback = document.getElementById('formFeedback');
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const nombre = document.getElementById('nombre');
            const email = document.getElementById('email');
            const telefono = document.getElementById('telefono');
            const mensaje = document.getElementById('mensaje');
            let valid = true;

            // Validación básica
            [nombre, email, mensaje].forEach(function(field) {
                field.classList.remove('error');
                if (!field.value.trim()) {
                    field.classList.add('error');
                    valid = false;
                }
            });

            if (email.value.trim() && !email.value.includes('@')) {
                email.classList.add('error');
                valid = false;
            }

            if (!valid) {
                formFeedback.textContent = '⚠️ Por favor completa todos los campos correctamente.';
                formFeedback.style.color = '#ff6b6b';
                return;
            }

            // Mostrar mensaje de "enviando..."
            formFeedback.textContent = '⏳ Enviando mensaje...';
            formFeedback.style.color = '#b0b8d1';

            // Preparar datos
            const formData = new FormData(this);

            // Enviar con fetch a enviar_mensaje.php
            fetch('enviar_mensaje.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Verificar que la respuesta sea JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('El servidor no devolvió una respuesta JSON válida.');
                }
                return response.json();
            })
            .then(data => {
                // Depuración: mostrar la respuesta completa en consola
                console.log('Respuesta del servidor:', data);

                if (data.success) {
                    formFeedback.textContent = data.message || '✅ Mensaje enviado con éxito.';
                    formFeedback.style.color = '#00f5d4';
                    contactForm.reset();
                    if (data.warning) {
                        formFeedback.textContent += ' ' + data.warning;
                    }
                } else {
                    // Mostrar el mensaje de error del servidor, o un mensaje genérico si no existe
                    const errorMsg = data.message || data.error || data.debug || 'Error desconocido. Intenta nuevamente.';
                    formFeedback.textContent = '❌ ' + errorMsg;
                    formFeedback.style.color = '#ff6b6b';
                }
            })
            .catch(error => {
                // Error de red o de parsing
                formFeedback.textContent = '❌ Error de conexión. Intenta nuevamente.';
                formFeedback.style.color = '#ff6b6b';
                console.error('Error en fetch:', error);
            });
        });
    }

    // 8. NEWSLETTER
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const input = newsletterForm.querySelector('input[type="email"]');
            const feedback = document.getElementById('newsletterFeedback');
            if (input.value.trim() && input.value.includes('@')) {
                feedback.textContent = '✅ ¡Suscripción exitosa! Revisa tu correo.';
                feedback.style.color = 'var(--cyan)';
                newsletterForm.reset();
            } else {
                feedback.textContent = '⚠️ Ingresa un correo válido.';
                feedback.style.color = '#ff6b6b';
            }
        });
    }

    // 9. PARALLAX
    const heroContent = document.querySelector('.hero-content');
    if (heroContent) {
        window.addEventListener('scroll', function() {
            const scrolled = window.scrollY;
            if (scrolled < window.innerHeight) {
                heroContent.style.transform = 'translateY(' + (scrolled * 0.05) + 'px)';
            }
        });
    }

    console.log('🚀 GEINFTEC S.A.S. – Sitio web cargado exitosamente.');
})();