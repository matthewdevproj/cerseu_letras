{{--
    Modal de confirmación de eliminación reutilizable (Alpine.js).

    Se coloca UNA sola vez (en el layout admin) y escucha un evento global.
    Cualquier botón lo abre despachando un CustomEvent 'confirm-delete' con la
    URL de destroy, el nombre del registro y el título a mostrar:

        <button type="button"
            onclick="window.dispatchEvent(new CustomEvent('confirm-delete', {
                detail: {
                    action: '{{ route('admin.docentes.destroy', $docente) }}',
                    name: '{{ addslashes($docente->nombre_completo) }}',
                    title: '¿Eliminar docente?'
                }
            }))">...</button>

    El nombre se pinta con x-text (nunca x-html), por lo que es seguro frente a
    inyección de HTML aunque provenga de datos editables por el administrador.

    Un solo x-show (sobre el overlay) evita conflictos de transición anidada:
    el backdrop queda detrás y la tarjeta encima; clic en el backdrop cierra.
--}}
<div
    x-data="{
        isOpen: false,
        action: '',
        name: '',
        title: '¿Confirmar eliminación?',
        show(detail) {
            this.action = detail?.action || '';
            this.name = detail?.name || '';
            this.title = detail?.title || '¿Confirmar eliminación?';
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
        }
    }"
    @confirm-delete.window="show($event.detail)"
    @keydown.escape.window.capture="isOpen && close()"
    x-show="isOpen"
    x-trap.inert.noscroll="isOpen"
    x-cloak
    role="dialog"
    aria-modal="true"
    aria-labelledby="confirm-delete-title"
    class="fixed inset-0 z-[80] flex items-center justify-center p-4">

    {{-- Backdrop (detrás de la tarjeta): un clic cierra --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="close()"></div>

    {{-- Tarjeta --}}
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <x-fas-exclamation-triangle class="text-3xl text-red-600" aria-hidden="true" />
            </div>
            <h3 id="confirm-delete-title" class="text-xl font-bold text-gray-900 mb-2" x-text="title"></h3>
            <p class="text-gray-600">
                Vas a eliminar <strong x-text="'“' + name + '”'"></strong>.
                Esta acción no se puede deshacer.
            </p>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button
                type="button"
                @click="close()"
                class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-400">
                Cancelar
            </button>
            <form :action="action" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="w-full px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 font-medium transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700">
                    Sí, eliminar
                </button>
            </form>
        </div>
    </div>
</div>
