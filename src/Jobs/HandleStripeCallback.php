<?php

namespace Dbaeka\StripePayment\Jobs;

use DateTime;
use Throwable;
use Stripe\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Dbaeka\StripePayment\Models\StripeCallback;

class HandleStripeCallback implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public array $payload
    ) {
    }

    public function handle(): void
    {
        try {
            $event = Event::constructFrom($this->payload);
            if (in_array($event->type, ['charge.succeeded', 'charge.failed'])) {
                $object = $event->data->object; // @phpstan-ignore-line
                $charge_id = $object->id;
                StripeCallback::query()->create([
                    'charge_id' => $charge_id,
                    'status' => str_replace('charge.', '', $event->type),
                    'response' => $object,
                ]);
            }
        } catch (Throwable $e) {
            Log::error('Failed parsing callback event. Error', [$e]);
        }
    }

    /**
     * Determine the time at which the job should timeout.
     * @codeCoverageIgnore
     */
    public function retryUntil(): DateTime
    {
        return now()->addMinutes(10);
    }
}
