<?php
$pageTitle = "Términos y Condiciones";
$cssPath = "../CSS/terminos.css";
include __DIR__ . '/../include/header.php';
?>
<section class="legal-hero">
    <div class="legal-hero__content">
        <p class="legal-hero__kicker">Legal</p>
        <h2 class="legal-hero__title">Términos y Condiciones</h2>
        <p class="legal-hero__meta">
            Última actualización: <time datetime="2026-04-15">16 de abril de 2026</time>
        </p>
        <p class="legal-hero__subtitle">
        Bienvenido al Portal de Acceso Estudiantil. Al acceder o utilizar nuestro sitio web, 
        usted acepta cumplir y estar sujeto a los siguientes términos y condiciones. 
        Si no está de acuerdo con alguna parte de estos términos, le solicitamos que no utilice 
        la plataforma.
        </p>
    </div>
</section>
<section class="legal">
    <div class="legal__container">
        <aside class="legal-toc" aria-label="Tabla de contenido">
            <h3 class="legal-toc__title">Contenido</h3>
            <ol class="legal-toc__list">
                <li><a href="#aceptacion">1. Aceptación de los términos</a></li>
                <li><a href="#cuentas">2. Cuentas y registro</a></li>
                <li><a href="#uso">3. Uso permitido</a></li>
                <li><a href="#propiedad">4. Propiedad intelectual</a></li>
                <li><a href="#privacidad">5. Privacidad</a></li>
                <li><a href="#responsabilidad">6. Limitación de responsabilidad</a></li>
                <li><a href="#cambios">7. Cambios a los términos</a></li>
                <li><a href="#contacto">8. Contacto</a></li>
            </ol>
        </aside>
        <div class="legal-content">
            <article class="legal-section" id="aceptacion">
                <h3>1. Aceptación de los términos</h3>
                <p>
                El acceso a esta página está condicionado a la aceptación y cumplimiento 
                de los términos aquí expuestos. Estos términos se aplican a todos los estudiantes 
                y usuarios que accedan al sistema.
                </p>
            </article>
            <article class="legal-section" id="cuentas">
                <h3>2. Cuentas y registro</h3>
                <p>
                Para hacer uso de los servicios de esta plataforma, el usuario debe ser un estudiante activo de la institución. 
                Al registrarse, usted garantiza que toda la información proporcionada es veraz, exacta y actual. El acceso es personal e intransferible; 
                queda terminantemente prohibido compartir credenciales de acceso con terceros. 
                El sistema se reserva el derecho de dar de baja cuentas que proporcionen información falsa o que comprometan la seguridad del sitio.
                </p>
            </article>
            <article class="legal-section" id="uso">
                <h3>3. Uso permitido</h3>
                <ul>
                    <li>Identificación: El acceso está restringido a estudiantes registrados con credenciales institucionales válidas.</li>
                    <li>Seguridad de Cuenta: El usuario es responsable de la protección de su contraseña. Cualquier actividad realizada desde su cuenta se considerará responsabilidad del titular.</li>
                    <li>Uso Académico: Esta plataforma tiene fines estrictamente educativos y de gestión académica. Queda prohibido cualquier uso comercial o lucrativo.</li>
                </ul>
            </article>
            <article class="legal-section" id="propiedad">
                <h3>4. Propiedad intelectual</h3>
                <p>
                Todo el contenido disponible en este sitio, incluyendo de forma enunciativa pero no limitativa: logotipos, diseños de interfaz, 
                código fuente (HTML, CSS, JavaScript, PHP), textos, gráficos y bases de datos, es propiedad intelectual del desarrollador o de sus respectivos titulares de derechos. 
                Se prohíbe cualquier intento de copia, ingeniería inversa, distribución o modificación sin el consentimiento expreso y por escrito del autor. 
                El uso de la plataforma no otorga ninguna licencia sobre las marcas o diseños aquí expuestos.
                </p>
            </article>
            <article class="legal-section" id="privacidad">
                <h3>5. Privacidad</h3>
                <p>
                El manejo de sus datos personales se rige bajo nuestra Política de Privacidad. 
                Los datos recolectados (nombres, correos electrónicos y registros académicos) se utilizan exclusivamente para la autenticación y mejora de la experiencia del usuario. 
                No compartimos su información con entidades externas con fines comerciales. 
                Al aceptar estos términos, usted consiente el tratamiento de sus datos bajo los protocolos de seguridad implementados en este servidor.
                </p>
            </article>
            <article class="legal-section" id="responsabilidad">
                <h3>6. Limitación de responsabilidad</h3>
                <p>
                La plataforma se proporciona "tal cual" y según disponibilidad. 
                No garantizamos que el servicio sea ininterrumpido o esté libre de errores técnicos. 
                No nos hacemos responsables por la pérdida de datos derivada de fallos en el servidor, ataques cibernéticos de terceros o el mal uso de la cuenta por parte del usuario. 
                Asimismo, no nos responsabilizamos por el contenido de enlaces externos que puedan aparecer en el portal con fines de referencia académica.
                </p>
            </article>
            <article class="legal-section" id="cambios">
                <h3>7. Cambios a los términos</h3>
                <p>
                Nos reservamos el derecho de actualizar o modificar estos términos en cualquier momento para adaptarlos a novedades legislativas o mejoras técnicas. 
                Cualquier cambio será publicado en esta misma sección y entrará en vigor de forma inmediata tras su publicación. 
                Se recomienda a los estudiantes revisar periódicamente este apartado para estar al tanto de sus obligaciones y derechos.
                </p>
            </article>
            <article class="legal-section" id="contacto">
                <h3>8. Contacto</h3>
                <p>
                Para cualquier duda, aclaración o reporte técnico relacionado con estos términos, 
                puede ponerse en contacto con el administrador del sistema a través del correo electrónico institucional de soporte o mediante el formulario de contacto 
                ubicado en el panel principal del estudiante.
                </p>
            </article>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../include/footer.php'; ?>
