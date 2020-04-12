<?php


namespace App\Repositories;


use App\Models\Server;

class ServerRepository
{
    protected $server;
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function getServerById($id){
        return $this->server->find($id);
    }
}