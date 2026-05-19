document.addEventListener('DOMContentLoaded', () => {

    //ELIMINAR MASCOTA DESDE USUARIO (con ajax)
    document.querySelectorAll('.btn-eliminar-mascota').forEach(btn => {
        btn.addEventListener('click', function () {
            const mascotaId = this.dataset.id;
            const nombre = this.dataset.nombre;
            const fila = this.closest('.mascota-card') || this.closest('li') || this.closest('tr'); //elemento que contiene la mascota


            window.abrirModal(
                'Eliminar mascota',
                `¿Seguro que quieres borrar "${nombre}"?`,
                () => {
                    //callback que se ejecuta si el usuario confirma
                    window.ajax(`/mascotas/${mascotaId}`, 'DELETE', {}, () => {
                        if (fila) {
                            const estado = fila.dataset.aprobado;

                            const contadorTotal = document.getElementById('contador-total-mascotas');
                            const contadorAprobadas = document.getElementById('contador-aprobadas');
                            const contadorPendientes = document.getElementById('contador-pendientes');
                            const contadorNoAprobadas = document.getElementById('contador-no-aprobadas');

                            //restar total
                            if (contadorTotal) {
                                contadorTotal.textContent = Math.max(0, parseInt(contadorTotal.textContent) - 1);
                            }
                            //restar segun estado
                            if (estado === 'aprobada' && contadorAprobadas) {
                                contadorAprobadas.textContent = Math.max(0, parseInt(contadorAprobadas.textContent) - 1);
                            }
                            if (estado === 'pendiente' && contadorPendientes) {
                                contadorPendientes.textContent = Math.max(0, parseInt(contadorPendientes.textContent) - 1);
                            }
                            if (estado === 'no-aprobada' && contadorNoAprobadas) {
                                contadorNoAprobadas.textContent = Math.max(0, parseInt(contadorNoAprobadas.textContent) - 1);
                            }
                            //borrar mascota
                            fila.remove();
                        }

                        window.mostrarMensaje('Mascota eliminada correctamente');
                    });
                }
            );
        });
    });

    //CANCELAR ESTANCIA DESDE USUARIO
    document.querySelectorAll('.btn-cancelar-estancia').forEach(btn => {
        btn.addEventListener('click', function () {
            const estanciaId = this.dataset.id;
            const mensaje = this.dataset.msg || '¿Seguro que quieres cancelar esta estancia?';

            window.abrirModal(
                'Cancelar estancia',
                mensaje,
                () => {
                    const form = document.getElementById(`form-cancelar-${estanciaId}`);
                    if (form) form.submit();
                }
            );
        });
    });

});
