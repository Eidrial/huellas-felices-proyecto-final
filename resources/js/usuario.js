document.addEventListener('DOMContentLoaded', () => {

    //funcion base 
    function ajax(url, method, data, onSuccess) {
        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
            .then(res => {
                if (!res.ok) throw new Error('Error en la petición');
                return res.json();
            })
            .then(onSuccess)
            .catch(err => {
                mostrarMensaje('Error al realizar la acción', true);
                console.error(err);
            });
    }

    //contenedor para mensajes
    const mensajeAjax = document.getElementById('mensaje-ajax');

    //mostrar mensajes temporales en pantalla
    //esError: si true -> mensaje rojo, si false -> mensaje verde
    function mostrarMensaje(msg, esError = false) {
        mensajeAjax.textContent = msg; //poner el texto del mensaje
        //aplicar clases de estilo segun si es error o exito
        mensajeAjax.className = `p-3 mb-4 rounded text-center font-semibold ${esError ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`;
        mensajeAjax.classList.remove('hidden');
        setTimeout(() => mensajeAjax.classList.add('hidden'), 4000);
    }

    // modal global
    const modal = document.getElementById('modal-confirmacion');
    const modalTexto = document.getElementById('modal-texto');
    const btnCancelar = document.getElementById('modal-cancelar');
    const btnConfirmar = document.getElementById('modal-confirmar');

    //callback que se ejecutara cuando el usuario confirme la accion
    let accionConfirmada = null;

    function abrirModal(texto, onConfirm) {
        modalTexto.textContent = texto;
        accionConfirmada = onConfirm;
        modal.classList.remove('hidden');
    }

    btnCancelar.addEventListener('click', () => {
        modal.classList.add('hidden');
        accionConfirmada = null;
    });

    btnConfirmar.addEventListener('click', () => {
        if (accionConfirmada) accionConfirmada();
        modal.classList.add('hidden');
        accionConfirmada = null;
    });

    //ELIMINAR MASCOTA DESDE USUARIO
    document.querySelectorAll('.btn-eliminar-mascota').forEach(btn => {
        btn.addEventListener('click', function () {
            const mascotaId = this.dataset.id;
            const nombre = this.dataset.nombre;
            const fila = this.closest('li'); //elemento de la lista que contiene la mascota

            abrirModal(
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
