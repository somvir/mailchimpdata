<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\FileUploaderModel;
use Spatie\Newsletter\Facades\Newsletter;
use Illuminate\Support\Facades\Http;

class ProcessMailchimpSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

     protected $baseUrl;
    protected $apiKey;
    protected $listId;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->apiKey = config('services.mailchimp.key');
        $this->listId = config('services.mailchimp.list_id');
        $dc = config('services.mailchimp.dc');

        $this->baseUrl = "https://{$dc}.api.mailchimp.com/3.0";

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $records = FileUploaderModel::all();

        foreach ($records as $record) {

        $url = "{$this->baseUrl}/lists/{$this->listId}/members";

        $response = Http::withBasicAuth('anystring', $this->apiKey)
            ->post($url, [
                'email_address' => $record->email,
                'status' => 'subscribed', // or 'pending' for double opt-in
                'merge_fields' => [
                    'FNAME' => $record->first_name,
                    'LNAME' => $record->last_name,
                    'TAGS'  => explode(',', $record->tags),
                ],
            ]);

            \Log::info("Response for " . $record->email . ": " . $response->body());

            \Log::info("Syncing user: " . $record->email);
        }
    }
}
