<?php

namespace App\Console\Commands;

use App\Kafka\Consumers\ProcessOrderHandler;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;

class ListenKafkaOrder extends Command
{
    /**
     * Execute the console command.
     */

    protected $signature = 'kafka:consume-orders';
    protected $description = 'Mendengarkan event';
    public function handle()
    {
        $consumer = Kafka::consumer()
            ->subscribe(['order-placed'])
            ->withConsumerGroupId('laravel-ecommerce-group')
            ->withHandler(new ProcessOrderHandler())
            ->build();

        $this->info("Sukses Terhubung, Menunggu Pesanan Masuk");

        $consumer->consume();
    }
}
