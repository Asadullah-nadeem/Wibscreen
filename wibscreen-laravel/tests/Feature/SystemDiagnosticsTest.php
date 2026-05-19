<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SystemDiagnosticsTest extends TestCase
{
    private string $webHost;
    private string $mailpitHost;

    protected function setUp(): void
    {
        parent::setUp();

        // Bypass phpunit.xml overrides to connect to the actual container services
        config()->set('database.connections.mysql.database', 'Wibscreen');
        config()->set('cache.default', 'redis');
        config()->set('queue.default', 'redis');

        // Dynamically determine host addresses based on the environment (Docker container vs. Windows host)
        $isDocker = file_exists('/.dockerenv') || env('DB_HOST') === 'db';
        $this->webHost = $isDocker ? 'web' : 'localhost:8000';
        $this->mailpitHost = $isDocker ? 'mailpit:8025' : 'localhost:8025';
    }
    /**
     * Test A: Database is connected and writable.
     */
    public function test_database_is_connected_and_writable(): void
    {
        $connection = DB::connection('mysql');
        $dbName = $connection->getDatabaseName();
        $this->assertNotEmpty($dbName);

        // Perform test query
        $connection->statement('CREATE TABLE IF NOT EXISTS test_diagnostics (id INT AUTO_INCREMENT PRIMARY KEY, val VARCHAR(255))');
        $connection->table('test_diagnostics')->insert(['val' => 'hello_db']);
        
        $row = $connection->table('test_diagnostics')->where('val', 'hello_db')->first();
        $this->assertNotNull($row);
        $this->assertEquals('hello_db', $row->val);

        // Clean up
        $connection->statement('DROP TABLE test_diagnostics');
    }

    /**
     * Test B: Cache driver is working.
     */
    public function test_cache_is_working(): void
    {
        $cache = Cache::store('redis');
        $cache->put('diagnostic_key', 'hello_cache', 10);
        $value = $cache->get('diagnostic_key');
        $this->assertEquals('hello_cache', $value);
        $cache->forget('diagnostic_key');
    }

    /**
     * Test C: Local Storage is writable.
     */
    public function test_storage_is_writable(): void
    {
        Storage::disk('local')->put('diagnostic_test.txt', 'hello_storage');
        $this->assertTrue(Storage::disk('local')->exists('diagnostic_test.txt'));
        $this->assertEquals('hello_storage', Storage::disk('local')->get('diagnostic_test.txt'));
        Storage::disk('local')->delete('diagnostic_test.txt');
    }

    /**
     * Test D: Mailpit SMTP and API check.
     */
    public function test_mailpit_smtp_and_api_are_working(): void
    {
        // Skip if SMTP host is not Mailpit
        $smtpHost = config('mail.mailers.smtp.host') ?? env('MAIL_HOST');
        if (!in_array($smtpHost, ['mailpit', '127.0.0.1', 'localhost'])) {
            $this->markTestSkipped('SMTP host is not Mailpit.');
        }

        // Send a test email
        Mail::mailer('smtp')->raw('This is a test email sent from diagnostics.', function ($message) {
            $message->to('user@example.com')->subject('System Diagnostic Test Email');
        });

        // Wait a split second for Mailpit to process it
        usleep(500000); // 0.5s

        // Query Mailpit REST API to verify it received the email
        $response = Http::get("http://{$this->mailpitHost}/api/v1/messages");
        
        $this->assertTrue($response->successful());
        $messages = $response->json('messages') ?? [];
        
        // Find our email
        $found = false;
        foreach ($messages as $msg) {
            if ($msg['Subject'] === 'System Diagnostic Test Email') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Email was not found in Mailpit mailbox');
    }

    /**
     * Test E: Queue worker is processing jobs.
     */
    public function test_queue_worker_is_processing_jobs(): void
    {
        $cache = Cache::store('redis');
        $cache->forget('diagnostic_queue_result');

        // Dispatch the job to the Redis queue connection
        \App\Jobs\TestQueueJob::dispatch()->onConnection('redis');

        // Poll the Redis cache for up to 15 seconds
        $processed = false;
        for ($i = 0; $i < 15; $i++) {
            if ($cache->get('diagnostic_queue_result') === 'success') {
                $processed = true;
                break;
            }
            sleep(1);
        }

        $this->assertTrue($processed, 'Queue job was not processed by the queue worker container');
        $cache->forget('diagnostic_queue_result');
    }

    /**
     * Test F: Load Balancer is distributing traffic to different PHP-FPM containers.
     */
    public function test_load_balancer_is_distributing_traffic(): void
    {
        $responses = [];
        
        // Make 10 requests to the load balancer (web service container)
        for ($i = 0; $i < 10; $i++) {
            $response = Http::withHeaders(['Connection' => 'close'])->get("http://{$this->webHost}/load-balancer-test");
            if ($response->successful()) {
                $responses[] = $response->json('handled_by');
            }
            usleep(50000); // 50ms
        }

        // Count unique backend containers that responded
        $uniqueBackends = array_unique(array_filter($responses));

        $this->assertNotEmpty($uniqueBackends, 'Load balancer did not return any backend responses');
        $this->assertGreaterThan(1, count($uniqueBackends), 'Load balancer is not distributing traffic across multiple instances');
        
        // Assert that the returned backends are valid app containers (app1-app5)
        foreach ($uniqueBackends as $backend) {
            $this->assertMatchesRegularExpression('/^app[1-5]$/', $backend);
        }
    }
}
