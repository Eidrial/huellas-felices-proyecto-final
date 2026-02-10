import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    //helpers globales (modal + mensaje + ajax)

    //MENSAJE GLOBAL AJAX
    const mensajeAjax = document.getElementById('mensaje-ajax');

    //mostrar mensajes temporales en pantalla
    //esError: true = estilo rojo, false = estilo verde
    window.mostrarMensaje = function (msg, esError = false) {
        mensajeAjax.textContent = msg;
        mensajeAjax.classList.remove('hidden');
        mensajeAjax.className = `p-3 mb-4 rounded text-center font-semibold ${esError ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`;
        //ocultar mensaje despues de 4 segundos
        setTimeout(() => mensajeAjax.classList.add('hidden'), 4000);
    }

    //MODAL GLOBAL
    const modal = document.getElementById('modal-confirmacion');
    const modalTitulo = document.getElementById('modal-titulo');
    const modalTexto = document.getElementById('modal-texto');
    const btnConfirmar = document.getElementById('modal-confirmar');
    const btnCancelar = document.getElementById('modal-cancelar');

    //callback que se ejecutara cuando el usuario confirme la accion
    let accionConfirmada = null;

    //abre el modal con titulo, texto y callback
    window.abrirModal = function (titulo, texto, onConfirm) {
        modalTitulo.textContent = titulo;
        modalTexto.textContent = texto;
        //onConfirm: callback que se ejecutara solo si el usuario confirma la accion
        accionConfirmada = onConfirm;
        modal.classList.remove('hidden');
    };

    //variable global para acciones que se ejecutan al cancelar el modal
    window.onModalCancel = null;

    //cancelar modal
    if (btnCancelar) {
        btnCancelar.addEventListener('click', () => {
            modal.classList.add('hidden');
            //limpiar la accion de confirmacion
            accionConfirmada = null;

            //si existe una accion definida para cuando se cancela el modal, se ejecuta
            if (window.onModalCancel) {
                window.onModalCancel();
                //limpiar para que no afecte a futuros modales
                window.onModalCancel = null;
            }
        });
    }

    //confirmar accion
    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', () => {
            //si hay accian asignada, se ejecuta
            if (accionConfirmada) accionConfirmada();
            //ocultar modal y limpiar callback
            modal.classList.add('hidden');
            accionConfirmada = null;
        });
    }

    //FUNCIÓN AJAX BASE
    window.ajax = function (url, method, data, onSuccess) {
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                //token CSRF necesario para peticiones POST/PUT/DELETE en Laravel
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
            .then(res => {
                if (!res.ok) throw new Error('Error en la petición');
                return res.json();
            })
            .then(onSuccess) //llama al callback pasando los datos json recibidos
            .catch(err => {
                window.mostrarMensaje('Error al realizar la acción', true);
                console.error(err);
            });
    };
});