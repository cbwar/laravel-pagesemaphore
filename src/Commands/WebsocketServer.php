<?php

namespace Cbwar\Laravel\PageSemaphore\Commands;

use Cbwar\Laravel\PageSemaphore\Libraries\WebSocket;
use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class WebsocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lancer le serveur websocket';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Lancement du serveur
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new WebSocket()
                )
            ),
            config('pagesemaphore.websocket.port')
        );

        $server->run();
    }
}
