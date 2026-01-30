document.addEventListener('DOMContentLoaded', () => {

    //FUNCIÓN AJAX BASE
    function ajax(url, method, data, onSuccess) {
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                //token CSRF necesario para peticiones POST/PUT/DELETE en Laravel
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: JSON.stringify(data)
        })
            .then(res => {
                if (!res.ok) throw new Error('Error en la petición');
                return res.json();
            })
            .then(onSuccess) //llama al callback pasando los datos json recibidos
            .catch(err => {
                mostrarMensaje('Error al realizar la acción', true);
                console.error(err);
            });
    }

    /*//MENSAJE GLOBAL AJAX
    //para mostrar mensajes de exito o error
    const mensajeAjax = document.getElementById('mensaje-ajax');

    //mostrar mensajes temporales en pantalla
    //esError: true -> estilo rojo, false -> estilo verde
    function mostrarMensaje(msg, esError = false) {
        mensajeAjax.textContent = msg;
        mensajeAjax.classList.remove('hidden');
        mensajeAjax.className = `p-3 mb-4 rounded text-center font-semibold ${esError ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`;
        //ocultar mensaje despues de 4 segundos
        setTimeout(() => mensajeAjax.classList.add('hidden'), 4000);
    }*/

    //MODAL GLOBAL
    const modal = document.getElementById('modal-confirmacion');
    const modalTitulo = document.getElementById('modal-titulo');
    const modalTexto = document.getElementById('modal-texto');
    const btnConfirmar = document.getElementById('modal-confirmar');
    const btnCancelar = document.getElementById('modal-cancelar');

    //callback que se ejecutara cuando el usuario confirme la accion
    let accionConfirmada = null;

    //abre el modal con titulo, texto y callback
    function abrirModal(titulo, texto, onConfirm) {
        modalTitulo.textContent = titulo;
        modalTexto.textContent = texto;
        //onConfirm: callback que se ejecutara solo si el usuario confirma la accion
        accionConfirmada = onConfirm;
        modal.classList.remove('hidden');
    }

    //cancelar modal
    btnCancelar.addEventListener('click', () => {
        modal.classList.add('hidden');
        accionConfirmada = null;
    });

    //confirmar accion
    btnConfirmar.addEventListener('click', () => {
        //si hay accian asignada, se ejecuta
        if (accionConfirmada) accionConfirmada();
        //ocultar modal y limpiar callback
        modal.classList.add('hidden');
        accionConfirmada = null;
    });

    //CAMBIAR ROL DE USUARIO
    //se aplica a todos los select con clase .cambiar-rol
    document.querySelectorAll('.cambiar-rol').forEach(select => {

        let rolAnterior = select.value; //guardar el rol previo por si se cancela

        select.addEventListener('change', function () {
            const userId = this.dataset.id;
            const nombre = this.dataset.nombre;
            const nuevoRol = this.value;
            const fila = this.closest('tr'); //fila de la tabla para actualizar

            abrirModal(
                'Confirmar cambio de rol',
                `¿Seguro que quieres cambiar el rol de ${nombre} a ${nuevoRol}?`,
                () => {
                    ajax(
                        `/admin/usuarios/${userId}/rol`,
                        'PUT',
                        { role: nuevoRol },
                        () => {
                            //actualizar texto en la tabla
                            //convierte la primera letra en mayus y concatena el resto del texto... ej: U-suario (el - no existiria, es para el ej)
                            fila.querySelector('.rol-text').textContent =
                                nuevoRol.charAt(0).toUpperCase() + nuevoRol.slice(1);
                            rolAnterior = nuevoRol;
                            mostrarMensaje('Rol actualizado correctamente');
                        }
                    );
                }
            );
        });

        //volver al rol anterior si cancela
        btnCancelar.addEventListener('click', () => {
            select.value = rolAnterior;
        });
    });

    //ELIMINAR USUARIO
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const userId = this.dataset.id;
            const nombre = this.dataset.nombre;
            const fila = this.closest('tr');

            abrirModal(
                'Eliminar usuario',
                `¿Seguro que quieres eliminar a ${nombre}?`,
                () => {
                    ajax(
                        `/admin/usuarios/${userId}`,
                        'DELETE',
                        {},
                        () => {
                            fila.remove();  //eliminar fila de la tabla
                            mostrarMensaje('Usuario eliminado correctamente');
                        }
                    );
                }
            );
        });
    });

    //ELIMINAR MASCOTA
    document.querySelectorAll('.btn-eliminar-mascota').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const mascotaId = this.dataset.id;
            const nombre = this.dataset.nombre;
            const fila = this.closest('tr');

            abrirModal(
                'Eliminar mascota',
                `¿Seguro que quieres eliminar a ${nombre}?`,
                () => {
                    ajax(
                        `/admin/mascotas/${mascotaId}`,
                        'DELETE',
                        {},
                        () => {
                            fila.remove();
                            mostrarMensaje('Mascota eliminada correctamente');
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
            ajax(`/admin/mascotas/${mascotaId}/aprobar`, 'PUT', { aprobado: valor }, (data) => {
                if (data.success) {
                    estadoSpan.textContent = data.texto; //actualiza texto
                    estadoSpan.className = data.color; //actualiza color
                    btn.parentElement.remove(); //quitar botones porque ya no hacen faltas
                    mostrarMensaje('Estado de la mascota actualizado');
                }
            });
        });

    });
});