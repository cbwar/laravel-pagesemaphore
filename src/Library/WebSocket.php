<?php

namespace Cbwar\Laravel\PageSemaphore\Libraries;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebSocket implements MessageComponentInterface
{
    protected $clients;
    private $_data = [];
    private $_urls = [];
    const DEBUG = true;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {

        $this->clients->attach($conn);
        $this->_debug('New connexion (' . $conn->resourceId . ')');
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if (!$json = json_decode($msg)) return;

        $this->_debug('json : ' . $msg);

        switch ($json->cmd) {

            // A chaque connexion on vérifie la dispo de la page
            case 'checkpage':

                // On enregistre les données de l'utilisateur dans un tableau
                $this->_data[$from->resourceId]['id'] = $json->id;
                $this->_data[$from->resourceId]['name'] = $json->name;

                // Si l'url n'as pas besoin d'être surveillée
                if ($json->url === '') {
                    $this->_data[$from->resourceId]['url'] = null;
                    return;
                }

                foreach ($this->_data as $resourceId => $v) {

                    // Quelqu'un est déjà sur cette page
                    if (isset($v['url']) && $v['url'] === $json->url && $resourceId !== $from->resourceId) {

                        // On envoi un message d'erreur à l'utilisateur qui tente d'accéder à la page
                        $from->send(json_encode(['cmd' => 'showerror', 'name' => $v['name']]));

                        // On notifie l'utilisateur qui est actuellement sur la page
                        foreach ($this->clients as $client) {
                            if ($client->resourceId !== $resourceId) continue;
                            $client->send(json_encode(['cmd' => 'notify', 'name' => $json->name]));
                        }

                        return;
                    }
                }

                // Personne n'est sur la page on enregistre l'url pour l'utilisateur courant
                $this->_data[$from->resourceId]['url'] = $json->url;

                break;
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        unset($this->_data[$conn->resourceId]);
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    private function _debug($msg)
    {
        if (!self::DEBUG) return;
        echo $msg . PHP_EOL;
    }
}