import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {

    //MENU HAMBURGUESA GLOBAL
    //?. evita error si el nav no existe en alguna pag
    document.getElementById('boton-menu')?.addEventListener('click', function () {
        document.getElementById('menu-movil').classList.toggle('hidden');
    });

    //mostrar y ocultar la contraseña al pulsar el icono
    window.mostrarContra = function (inputId, abiertoId, cerradoId) {
        //obtener el input de contra
        const input = document.getElementById(inputId);
        //obtener los iconos (ojo abierto y cerrado)
        const abierto = document.getElementById(abiertoId);
        const cerrado = document.getElementById(cerradoId);

        //si no existe el input, salir para evitar errores
        if (!input) {
            return;
        }

        //si la contra esta oculta
        if (input.type === 'password') {
            //mostrar la contraseña
            input.type = 'text';
            //cambiar iconos -> ocultar ojo abierto y mostrar cerrado
            if (abierto && cerrado) {
                abierto.classList.add('hidden');
                cerrado.classList.remove('hidden');
            }
        } else {
            //ocultar la contraseña
            input.type = 'password';
            //cambiar iconos -> mostrar ojo abierto y ocultar cerrado
            if (abierto && cerrado) {
                abierto.classList.remove('hidden');
                cerrado.classList.add('hidden');
            }
        }
    }

    //helpers globales (modal + mensaje + ajax)

    //MENSAJE GLOBAL AJAX
    const mensajeAjax = document.getElementById('mensaje-ajax');

    //mostrar mensajes temporales en pantalla
    //esError: true = estilo rojo, false = estilo verde
    window.mostrarMensaje = function (msg, esError = false) {
        if (mensajeAjax) {
            mensajeAjax.textContent = msg;
            mensajeAjax.classList.remove('hidden');
            mensajeAjax.className = `mensaje-sesion p-3 mb-4 rounded-xl text-center text-sm ${esError
                ? 'bg-red-100 text-red-700'
                : 'bg-[#eef5e8] text-[#2d5a27]'
                }`;
            //ocultar mensaje despues de 4 segundos
            setTimeout(() => mensajeAjax.classList.add('hidden'), 4000);
        }
    }

    //MODAL GLOBAL
    const modal = document.getElementById('modal-confirmacion');
    const modalTitulo = document.getElementById('modal-titulo');
    const modalTexto = document.getElementById('modal-texto');
    const btnConfirmar = document.getElementById('modal-confirmar');
    const btnCancelar = document.getElementById('modal-cancelar');

    //callback que se ejecutara cuando el usuario confirme la accion
    let accionConfirmada = null;

    if (modal && modalTitulo && modalTexto) {
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

    }

    //PERFIL !
    //REENVIAR VERIFICACIÓN EMAIL
    const btnReenviarVerificacion = document.getElementById('btn-reenviar-verificacion');
    const formVerificacion = document.getElementById('send-verification');

    if (btnReenviarVerificacion && formVerificacion) {
        btnReenviarVerificacion.addEventListener('click', function () {
            if (window.abrirModal) {
                window.abrirModal(
                    'Reenviar correo',
                    '¿Quieres reenviar el correo de verificación? Puede tardar unos minutos en llegar.',
                    function () {
                        formVerificacion.submit();
                    }
                );
            }
        });
    }

    //ELIMINAR CUENTA
    const btnEliminarCuenta = document.getElementById('btn-eliminar-cuenta');
    const formEliminarCuenta = document.getElementById('form-eliminar-cuenta');

    if (btnEliminarCuenta && formEliminarCuenta) {
        btnEliminarCuenta.addEventListener('click', function () {

            window.abrirModal(
                'Eliminar cuenta',
                '¿Seguro que quieres eliminar tu cuenta? Esta acción no se puede deshacer.',
                function () {
                    formEliminarCuenta.submit();
                }
            );

        });
    }

    //ocultar mensajes normales (sesion) despues de 4 segundos (para que sea igual que los que usan ajax)
    const mensajesSession = document.querySelectorAll('.mensaje-sesion');

    mensajesSession.forEach(msg => {
        setTimeout(() => {
            msg.classList.add('hidden');
        }, 4000);
    });

    // MOSTRAR NOMBRE DEL ARCHIVO DE FOTO
    const inputFoto = document.getElementById('foto');
    const nombreArchivo = document.getElementById('nombre-archivo');

    if (inputFoto && nombreArchivo) {
        inputFoto.addEventListener('change', function () {
            if (this.files.length > 0) {
                nombreArchivo.textContent = this.files[0].name;
            } else {
                nombreArchivo.textContent = 'Ningún archivo seleccionado';
            }
        });
    }

    //FUNCIÓN AJAX BASE
    window.ajax = function (url, method, data, onSuccess) {
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                //token CSRF necesario para peticiones POST/PUT/DELETE en Laravel
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(data => {
                if (data.success == false) {
                    window.mostrarMensaje(data.message || 'No se pudo realizar la acción', true);
                    return;
                }
                onSuccess(data);
            })
            .catch(err => {
                window.mostrarMensaje('Error al realizar la acción', true);
                console.error(err);
            });
    };

});