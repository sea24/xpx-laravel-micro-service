<?php

namespace Gzoran\LaravelMicroService\Commands;

use Gzoran\LaravelMicroService\Clients\Contracts\ServiceCenterDriverContract;
use Gzoran\LaravelMicroService\Clients\Exceptions\ClientException;
use Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers\LocalServiceCenterDriver;
use Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers\RemoteServiceCenterDriver;
use Illuminate\Console\Command;

/**
 * 注销服务
 * Class LogoutServer
 *
 * @package Gzoran\LaravelMicroService\Commands
 */
class LogoutServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'microservice:logout_server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logout server\'s nodes in service center.';

    /**
     * 服务中心驱动
     *
     * @var ServiceCenterDriverContract
     */
    protected $serviceCenterDriver;

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
     * @throws ClientException
     */
    public function handle()
    {
        // 服务中心驱动
        switch ($driver = config('microservice.service_center_driver.default', 'local')) {
            case 'remote':
                $driverClass = config('microservice.service_center_driver.remote', RemoteServiceCenterDriver::class);
                break;
            case 'local':
                $this->warn('Your service center driver is local.');
                $driverClass = config('microservice.service_center_driver.local', LocalServiceCenterDriver::class);
                break;
            default:
                throw new ClientException('Cat not match service center driver like:' . $driver);
        }
        $this->serviceCenterDriver = new $driverClass;

        $serverRegisters = config('microservice.server_registers');

        $tableRow = [];
        $i = 0;
        foreach ($serverRegisters as $serverRegister) {
            $serverName = $serverRegister['server_name'];
            $this->serviceCenterDriver->logoutServer($serverName, $serverRegister['nodes']);
            $url = '';
            foreach ($serverRegister['nodes'] as $node) {
                $url .= trim("{$node['scheme']}://{$node['host']}:{$node['port']}/{$node['path']}", "/") . "\r\n";
            }
            $tableRow[$i][] = $serverName;
            $tableRow[$i][] = trim($url);
            $i++;
        }

        $this->info('The servers has been logout!');

        $this->table([
            'Server Name',
            'Nodes',
        ], $tableRow);
    }
}
