document.addEventListener('DOMContentLoaded', () => {

    //CAMBIAR ROL DE USUARIO
    //se aplica a todos los select con clase .cambiar-rol
    document.querySelectorAll('.cambiar-rol').forEach(select => {

        let rolAnterior = select.value; //guardar el rol previo por si se cancela

        select.addEventListener('change', function () {
            const userId = this.dataset.id;
            const nombre = this.dataset.nombre;
            const nuevoRol = this.value;
            const fila = this.closest('tr'); //fila de la tabla para actualizar

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
                        () => {
                            //actualizar texto en la tabla
                            //convierte la primera letra en mayus y concatena el resto del texto... ej: U-suario (el - no existiria, es para el ej)
                            fila.querySelector('.rol-text').textContent =
                                nuevoRol.charAt(0).toUpperCase() + nuevoRol.slice(1);
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
            const fila = this.closest('tr');

            window.abrirModal(
                'Eliminar usuario',
                `¿Seguro que quieres eliminar a ${nombre}?`,
                () => {
                    window.ajax(
                        `/admin/usuarios/${userId}`,
                        'DELETE',
                        {},
                        () => {
                            fila.remove();  //eliminar fila de la tabla
                            window.mostrarMensaje('Usuario eliminado correctamente');
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
                            fila.remove();
                            window.mostrarMensaje('Mascota eliminada correctamente');
                        }
                    );
                }
            );
        });
    });

    //APROBAR / NO APROBAR MASCOTA (sin modal)
    document.querySelectorAll('.aprobar-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const mascotaId = this.dataset.id;
            const valor = parseInt(this.dataset.valor); //0 o 1
            const estadoSpan = document.getElementById(`estado-${mascotaId}`);

            //directo sin modal
            window.ajax(`/admin/mascotas/${mascotaId}/aprobar`, 'PUT', { aprobado: valor }, (data) => {
                if (data.success) {
                    estadoSpan.textContent = data.texto; //actualiza texto
                    estadoSpan.className = data.color; //actualiza color
                    btn.parentElement.remove(); //quitar botones porque ya no hacen faltas
                    window.mostrarMensaje(data.message || 'Estado de la mascota actualizado');
                }
            });
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

});