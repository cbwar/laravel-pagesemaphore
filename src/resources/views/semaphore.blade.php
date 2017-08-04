@push('js')
    <script>
        let semaphore_websocket_url = '{{ config('pagesemaphore.websocket.proto') }}://{{ config('pagesemaphore.websocket.hostname') }}:{{ config('pagesemaphore.websocket.port') }}';
        console.log(semaphore_websocket_url);

        let page_semaphore = new WebSocket(semaphore_websocket_url);

        page_semaphore.onopen = function (e) {
            page_semaphore.send(JSON.stringify({
                cmd: 'checkpage',
                id: "{{ Auth::user()->id }}",
                name: "{{ Auth::user()->name }}",
                url: "{{Request::url() }}"
            }))
        }
        ;

        page_semaphore.onmessage = function (e) {
            let json = jQuery.parseJSON(e.data);
            switch (json.cmd) {

                case 'showerror':
                    bootbox.dialog({
                        message: "Cette page est déjà en cours d'édition par l'utilisateur <strong>" + json.name + "</strong>",
                        closeButton: false,
                        keyboard: false,
                        buttons: {
                            main: {
                                label: "OK",
                                className: "btn-primary",
                                callback: function () {
                                    window.history.back()
                                }
                            }
                        }
                    });
                    break;

                case 'notify':
                    growl(json.name + " tente d'accéder à l'édition de cette page")
                    break
            }
        };
    </script>
@endpush