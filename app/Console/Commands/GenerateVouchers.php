<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Router;
use App\Models\Profile;
use App\Models\Voucher;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GenerateVouchers extends Command
{
    protected $signature = 'vouchers:generate
                            {router_id : Router ID}
                            {profile_id : Profile ID}
                            {count : How many vouchers to generate}
                            {--prefix= : Optional code prefix (e.g. MB)}
                            {--length=8 : Length of random part (without prefix)}';

    protected $description = 'Generate a batch of vouchers for a router/profile';

    public function handle(): int
    {
        $routerId  = (int) $this->argument('router_id');
        $profileId = (int) $this->argument('profile_id');
        $count     = (int) $this->argument('count');
        $prefix    = strtoupper((string) $this->option('prefix'));
        $length    = (int) $this->option('length');

        if ($count <= 0 || $length < 4) {
            $this->error('Invalid count or length.');
            return self::FAILURE;
        }

        $router = Router::find($routerId);
        if (! $router) {
            $this->error("Router {$routerId} not found.");
            return self::FAILURE;
        }

        $profile = Profile::where('router_id', $routerId)
            ->where('id', $profileId)
            ->first();

        if (! $profile) {
            $this->error("Profile {$profileId} not found for router {$routerId}.");
            return self::FAILURE;
        }

        $this->info("Generating {$count} vouchers for Router #{$routerId} ({$router->name}), Profile #{$profileId} ({$profile->name})");

        $generated = [];

        for ($i = 0; $i < $count; $i++) {
            $code = $this->generateUniqueCode($prefix, $length);

            $voucher = Voucher::create([
                'router_id'      => $routerId,
                'profile_id'     => $profileId,
                'code'           => $code,
                'face_value'     => $profile->price,
                'status'         => 'new',
                'expires_at'     => Carbon::now()->addDays(30), // default 30 days
                'customer_phone' => null,
            ]);

            $generated[] = $voucher->code;
            $this->line(" - {$voucher->code}");
        }

        $this->info("Done. Generated " . count($generated) . " vouchers.");

        return self::SUCCESS;
    }

    protected function generateUniqueCode(?string $prefix, int $length): string
    {
        $code = null;

        do {
            $random = strtoupper(Str::random($length));
            $candidate = $prefix ? ($prefix . $random) : $random;
        } while (Voucher::where('code', $candidate)->exists());

        return $candidate;
    }
}
