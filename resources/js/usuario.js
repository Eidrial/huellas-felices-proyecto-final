document.addEventListener('DOMContentLoaded', () => {

    //ELIMINAR MASCOTA DESDE USUARIO
    document.querySelectorAll('.btn-eliminar-mascota').forEach(btn => {
        btn.addEventListener('click', function () {
            const mascotaId = this.dataset.id;
            const nombre = this.dataset.nombre;
            const fila = this.closest('li'); //elemento de la lista que contiene la mascota

            window.abrirModal(
                'Eliminar mascota',
                `¿Seguro que quieres borrar "${nombre}"?`,
                () => {
                    //callback que se ejecuta si el usuario confirma
                    ajax(`/mascotas/${mascotaId}`, 'DELETE', {}, (data) => {
                        fila.remove();
                        mostrarMensaje('Mascota eliminada correctamente');
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
