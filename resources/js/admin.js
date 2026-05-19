document.addEventListener('DOMContentLoaded', () => {

    //MOSTRAR / OCULTAR DATOS PERSONALES SEGUN EL ROL AL CREAR USUARIO
    const selectorRolCrear = document.getElementById('role');
    const datosPersonalesCrear = document.getElementById('datos-personales-usuario');

    if (selectorRolCrear && datosPersonalesCrear) {

        //estado inicial
        if (selectorRolCrear.value === 'admin') {
            datosPersonalesCrear.classList.add('hidden');
        }

        //cambio de rol
        selectorRolCrear.addEventListener('change', function () {

            if (this.value === 'admin') {
                datosPersonalesCrear.classList.add('hidden');
            } else {
                datosPersonalesCrear.classList.remove('hidden');
            }

        });
    }

    //CAMBIAR ROL DE USUARIO
    //se aplica a todos los select con clase .cambiar-rol
    document.querySelectorAll('.cambiar-rol').forEach(select => {

        //guardar el rol previo por si se cancela
        let rolAnterior = select.value;

        select.addEventListener('change', function () {
            const userId = this.dataset.id;
            const nombre = this.dataset.nombre;
            const nuevoRol = this.value;

            //tabla - tarjetas
            const contenedor = this.closest('tr') || this.closest('[data-id]');


            //volver al rol anterior si cancela
            window.onModalCancel = () => {
                select.value = rolAnterior;
            };

            window.abrirModal(
                'Confirmar cambio de rol',
                `¿Seguro que quieres cambiar el rol de ${nombre} a ${nuevoRol}?`,
                () => {
                    window.ajax(
                        `/admin/usuarios/${userId}/rol`,
                        'PUT',
                        { role: nuevoRol },
                        (data) => {
                            const rolEl = contenedor.querySelector('.rol-text');

                            if (rolEl) {
                                rolEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full shrink-0 ${data.punto}"></span>
                                <span class="${data.etiqueta} text-sm">${data.texto}</span>`;
                            }

                            rolAnterior = nuevoRol;
                            window.onModalCancel = null;
                            window.mostrarMensaje('Rol actualizado correctamente');
                        }
                    );
                }
            );
        });
    });

    //ELIMINAR USUARIO
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const userId = this.dataset.id;
            const nombre = this.dataset.nombre;
            const fila = this.closest('tr') || this.closest('[data-id]');

            window.abrirModal(
                'Eliminar usuario',
                `¿Seguro que quieres eliminar a ${nombre}?`,
                () => {
                    window.ajax(
                        `/admin/usuarios/${userId}`,
                        'DELETE',
                        {},
                        (data) => {
                            if (!data.success) {
                                window.mostrarMensaje(data.message || 'No se pudo eliminar el usuario', 'error');
                                return;
                            }

                            if (fila) {
                                fila.remove();
                            }

                            window.mostrarMensaje(data.message || 'Usuario eliminado correctamente');
                        }
                    );
                }
            );
        });
    });

    //ELIMINAR MASCOTA
    document.querySelectorAll('.btn-eliminar-mascota-admin').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const mascotaId = this.dataset.id;
            const nombre = this.dataset.nombre;
            const fila = this.closest('tr');

            window.abrirModal(
                'Eliminar mascota',
                `¿Seguro que quieres eliminar a ${nombre}?`,
                () => {
                    window.ajax(
                        `/admin/mascotas/${mascotaId}`,
                        'DELETE',
                        {},
                        () => {
                            if (fila) {
                                fila.remove();
                            }

                            window.mostrarMensaje('Mascota eliminada correctamente');
                        }
                    );
                }
            );
        });
    });

    //APROBAR / NO APROBAR MASCOTA (con modal)
    document.querySelectorAll('.aprobar-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const mascotaId = this.dataset.id;
            const valor = parseInt(this.dataset.valor); //0 o 1
            const nombre = this.dataset.nombre;
            const estadoTexto = document.getElementById(`estado-${mascotaId}`);
            const estadoPunto = document.getElementById(`punto-${mascotaId}`);

            let titulo = '';
            let mensaje = '';

            if (valor === 1) {
                titulo = 'Aprobar mascota';
                mensaje = `¿Seguro que quieres aprobar a ${nombre}? Si tiene estancias pendientes, se intentarán confirmar automáticamente según disponibilidad.`;
            } else {
                titulo = 'No aprobar mascota';
                mensaje = `¿Seguro que quieres no aprobar a ${nombre}?`;
            }

            window.abrirModal(
                titulo,
                mensaje,
                () => {
                    window.ajax(`/admin/mascotas/${mascotaId}/aprobar`, 'PUT', { aprobado: valor }, (data) => {
                        if (data.success) {
                            if (estadoTexto) {
                                estadoTexto.textContent = data.texto; //actualiza texto
                                estadoTexto.className = `text-sm ${data.etiqueta}`; //actualiza color del texto
                            }

                            if (estadoPunto) {
                                estadoPunto.className = `w-1.5 h-1.5 rounded-full shrink-0 ${data.punto}`; //actualiza color del punto
                            }

                            if (btn.parentElement) {
                                btn.parentElement.remove(); //quitar botones porque ya no hacen falta
                            }

                            window.mostrarMensaje(data.message || 'Estado de la mascota actualizado');
                        }
                    });
                }
            );
        });
    });

    //CANCELAR ESTANCIA DESDE ADMIN
    document.querySelectorAll('.btn-cancelar-estancia-admin').forEach(btn => {
        btn.addEventListener('click', function () {
            const estanciaId = this.dataset.id;
            const msg = this.dataset.msg || '¿Seguro que quieres cancelar esta estancia?';

            window.abrirModal(
                'Cancelar estancia',
                msg,
                () => {
                    const form = document.getElementById(`form-cancelar-estancia-${estanciaId}`);
                    //saber que existe el form
                    if (form) {
                        form.submit();
                    }
                }
            );
        });
    });

    //CONFIRMAR ESTANCIA DESDE ADMIN (con modal)
    document.querySelectorAll('.btn-confirmar-estancia-admin').forEach(btn => {
        btn.addEventListener('click', function () {
            const estanciaId = this.dataset.id;
            const msg = this.dataset.msg;

            window.abrirModal(
                'Confirmar estancia',
                msg,
                () => {
                    const form = document.getElementById(`form-confirmar-estancia-${estanciaId}`);
                    if (form) form.submit();
                }
            );
        });
    });

    //INICIAR ESTANCIA DESDE ADMIN (con modal)
    document.querySelectorAll('.btn-iniciar-estancia-admin').forEach(btn => {
        btn.addEventListener('click', function () {
            const estanciaId = this.dataset.id;
            const msg = this.dataset.msg || '¿Seguro que quieres iniciar esta estancia?';

            window.abrirModal(
                'Iniciar estancia',
                msg,
                () => {
                    const form = document.getElementById(`form-iniciar-estancia-${estanciaId}`);
                    if (form) form.submit();
                }
            );
        });
    });

    //FINALIZAR ESTANCIA DESDE ADMIN (con modal)
    document.querySelectorAll('.btn-finalizar-estancia-admin').forEach(btn => {
        btn.addEventListener('click', function () {
            const estanciaId = this.dataset.id;
            const msg = this.dataset.msg || '¿Seguro que quieres finalizar esta estancia?';

            window.abrirModal(
                'Finalizar estancia',
                msg,
                () => {
                    const form = document.getElementById(`form-finalizar-estancia-${estanciaId}`);
                    if (form) form.submit();
                }
            );
        });
    });

    //BORRAR AVISO DESDE ADMIN (con modal)
    document.querySelectorAll('.btn-borrar-aviso-admin').forEach(btn => {
        btn.addEventListener('click', function () {
            const avisoId = this.dataset.id;
            const msg = this.dataset.msg || '¿Seguro que quieres borrar este aviso?';

            window.abrirModal(
                'Borrar aviso',
                msg,
                () => {
                    const form = document.getElementById(`form-borrar-aviso-${avisoId}`);
                    if (form) form.submit();
                }
            );
        });
    });

    //BORRAR EXTRA DESDE ADMIN (con modal)
    document.querySelectorAll('.btn-borrar-extra-admin').forEach(btn => {
        btn.addEventListener('click', function () {
            const cuidadoId = this.dataset.id;
            const msg = this.dataset.msg || '¿Seguro que quieres borrar este extra?';

            window.abrirModal(
                'Borrar extra',
                msg,
                () => {
                    const form = document.getElementById(`form-borrar-extra-${cuidadoId}`);
                    if (form) form.submit();
                }
            );
        });
    });

});