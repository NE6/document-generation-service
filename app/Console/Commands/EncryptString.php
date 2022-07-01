<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EncryptString extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encrypt:string';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $value = $this->secret("Paste or type the string you wish to encrypt.");
        $this->info("Your encrypted value:");
        $this->info(openssl_encrypt(
            $value,
            config('app.aws_inbound_api_decryption_method'),
            config('app.aws_inbound_api_decryption_key')
        ));

        return 0;
    }
}
