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

});
