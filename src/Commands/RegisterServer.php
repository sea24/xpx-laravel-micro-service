<?php

namespace Gzoran\LaravelMicroService\Commands;

use Gzoran\LaravelMicroService\Clients\Contracts\ServiceCenterDriverContract;
use Gzoran\LaravelMicroService\Clients\Exceptions\ClientException;
use Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers\LocalServiceCenterDriver;
use Gzoran\LaravelMicroService\Clients\ServiceCenterDrivers\RemoteServiceCenterDriver;
use Gzoran\LaravelMicroService\Exceptions\MicroServiceException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * 注册服务
 * Class RegisterServer
 *
 * @package Gzoran\LaravelMicroService\Commands
 */
class RegisterServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'microservice:register_server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register server and nodes to service center.';

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

        Validator::make($serverRegisters, [
            'server_name' => 'required|string|min:1',
            'nodes' => 'required|array|min:1',
            'nodes.*.scheme' => 'required|string|min:1|in:http,https,tcp',
            'nodes.*.host' => 'required|array|between,0,65535',
            'nodes.*.port' => 'required|int|min:1',
            'nodes.*.path' => 'string|min:1',
        ])->validate();

        $tableRow = [];
        $i = 0;
        foreach ($serverRegisters as $serverRegister) {
            $serverName = $serverRegister['server_name'];
            $this->serviceCenterDriver->registerServer($serverName, $serverRegister['nodes']);
            $url = '';
            foreach ($serverRegister['nodes'] as $node) {
                $url .= trim("{$node['scheme']}://{$node['host']}:{$node['port']}/{$node['path']}", "/") . "\r\n";
            }
            $tableRow[$i][] = $serverName;
            $tableRow[$i][] = trim($url);
            $i++;
        }

        $this->info('The servers has been registered!');

        $this->table([
            'Server Name',
            'Nodes',
        ], $tableRow);
    }
}
