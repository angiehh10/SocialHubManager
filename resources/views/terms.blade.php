{{-- resources/views/terms.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Términos de Servicio') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-900 shadow sm:rounded-lg p-6 sm:p-8 border border-gray-200 dark:border-zinc-800">
                <div class="prose dark:prose-invert max-w-none">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Última actualización: 22 de agosto de 2025</p>

                    <h3>1. Aceptación</h3>
                    <p>
                        Al acceder o utilizar <strong>Social Hub Manager</strong> (la “Aplicación”), aceptas estos
                        Términos de Servicio. Si no estás de acuerdo, no uses la Aplicación.
                    </p>

                    <h3>2. Cuenta y seguridad</h3>
                    <ul>
                        <li>Eres responsable de la confidencialidad de tus credenciales y de toda actividad en tu cuenta.</li>
                        <li>Debes proporcionar información veraz y mantenerla actualizada.</li>
                        <li>Podemos suspender o cerrar cuentas que violen estos términos o la ley.</li>
                    </ul>

                    <h3>3. Conexiones con redes sociales y tokens</h3>
                    <p>
                        La Aplicación se integra con servicios de terceros (p. ej., Reddit, Discord, Facebook). Al
                        conectar tus cuentas, autorizas a la Aplicación a obtener y usar los permisos necesarios (p. ej.,
                        identidad, email, publicar en páginas, etc.).
                    </p>
                    <ul>
                        <li>Los tokens de acceso se almacenan de forma segura y solo se usan para las funciones que autorizas.</li>
                        <li>Debes cumplir también los términos de cada plataforma conectada.</li>
                        <li>Es tu responsabilidad revocar permisos en las plataformas si ya no deseas usar la integración.</li>
                    </ul>

                    <h3>4. Contenido y uso aceptable</h3>
                    <ul>
                        <li>No publiques contenido ilegal, difamatorio, discriminatorio, que infrinja derechos de autor o marcas, ni spam.</li>
                        <li>No intentes eludir límites, interferir con la seguridad o realizar uso automatizado abusivo.</li>
                        <li>Nos reservamos el derecho de bloquear o eliminar contenido que viole estas reglas.</li>
                    </ul>

                    <h3>5. Propiedad intelectual</h3>
                    <p>
                        Conservas los derechos sobre tu contenido. Concedes a la Aplicación una licencia limitada para
                        procesarlo con el fin de brindar las funciones (programación, publicación, métricas, etc.).
                    </p>

                    <h3>6. Disponibilidad, cambios y soporte</h3>
                    <ul>
                        <li>Nos esforzamos por ofrecer disponibilidad razonable, pero el servicio se brinda “tal cual”.</li>
                        <li>Podemos modificar o interrumpir funciones (incluidas integraciones) sin responsabilidad hacia ti.</li>
                        <li>Las APIs de terceros pueden fallar, cambiar o limitarse; esto puede afectar funciones de la Aplicación.</li>
                    </ul>

                    <h3>7. Privacidad</h3>
                    <p>
                        Consulta nuestra <a href="{{ route('privacy') }}" class="underline">Política de Privacidad</a> para
                        conocer cómo recopilamos y tratamos tus datos.
                    </p>

                    <h3>8. Limitación de responsabilidad</h3>
                    <p>
                        En la medida máxima permitida por la ley, la Aplicación y sus responsables no serán responsables por
                        daños indirectos, incidentales, punitivos o consecuentes derivados del uso o imposibilidad de uso del servicio.
                    </p>

                    <h3>9. Terminación</h3>
                    <p>
                        Puedes dejar de usar la Aplicación en cualquier momento. Podemos suspender o terminar el acceso si
                        incumples estos términos o por riesgo de seguridad/abuso.
                    </p>

                    <h3>10. Cambios a los términos</h3>
                    <p>
                        Podemos actualizar estos términos. Publicaremos la versión vigente en este sitio. Si continúas usando la
                        Aplicación después de la actualización, se considera que aceptas los cambios.
                    </p>

                    <h3>11. Ley aplicable y jurisdicción</h3>
                    <p>
                        Estos términos se rigen por las leyes de Costa Rica, sin perjuicio de sus normas de conflicto. Cualquier
                        disputa se someterá a los tribunales competentes de Costa Rica.
                    </p>

                    <h3>12. Contacto</h3>
                    <p>
                        ¿Dudas o solicitudes? Escríbenos a
                        <a href="mailto:angiehh1724@gmail.com" class="underline">angiehh1724@gmail.com</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

