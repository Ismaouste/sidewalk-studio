<?php

namespace Tests\Feature;

use App\Newsletter\BrevoNewsletterDriver;
use App\Newsletter\LogNewsletterDriver;
use App\Newsletter\NewsletterDeliveryException;
use App\Newsletter\NewsletterDriver;
use ArrayObject;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class NewsletterSubscriptionTest extends TestCase
{
    private ArrayObject $subscriptions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriptions = new ArrayObject;
        $subscriptions = $this->subscriptions;

        $this->app->instance(NewsletterDriver::class, new class($subscriptions) implements NewsletterDriver
        {
            public function __construct(private ArrayObject $subscriptions) {}

            public function subscribe(string $email, string $segment, string $locale): void
            {
                $this->subscriptions->append(compact('email', 'segment', 'locale'));
            }
        });
    }

    public function test_a_subscription_is_accepted_without_setting_any_cookie(): void
    {
        $response = $this->postJson('/newsletter', [
            'email' => 'reader@example.com',
            'segment' => 'engineering',
            'locale' => 'fr',
        ]);

        $response->assertStatus(202);
        $response->assertJson(['status' => 'pending_confirmation']);
        $this->assertNull($response->headers->get('Set-Cookie'));

        $this->assertCount(1, $this->subscriptions);
        $this->assertSame(
            ['email' => 'reader@example.com', 'segment' => 'engineering', 'locale' => 'fr'],
            $this->subscriptions[0],
        );
    }

    public function test_a_filled_honeypot_is_swallowed_without_reaching_the_driver(): void
    {
        $this->postJson('/newsletter', [
            'email' => 'bot@example.com',
            'segment' => 'engineering',
            'locale' => 'en',
            'company_website' => 'https://spam.example',
        ])->assertStatus(202);

        $this->assertCount(0, $this->subscriptions);
    }

    public function test_an_invalid_payload_is_rejected(): void
    {
        $this->postJson('/newsletter', [
            'email' => 'not-an-email',
            'segment' => 'engineering',
            'locale' => 'en',
        ])->assertStatus(422);

        $this->postJson('/newsletter', [
            'email' => 'reader@example.com',
            'segment' => 'investors',
            'locale' => 'en',
        ])->assertStatus(422);

        $this->assertCount(0, $this->subscriptions);
    }

    public function test_a_failing_driver_surfaces_as_service_unavailable(): void
    {
        $this->app->instance(NewsletterDriver::class, new class implements NewsletterDriver
        {
            public function subscribe(string $email, string $segment, string $locale): void
            {
                throw new NewsletterDeliveryException('Brevo said no.');
            }
        });

        $this->postJson('/newsletter', [
            'email' => 'reader@example.com',
            'segment' => 'local-business',
            'locale' => 'en',
        ])->assertStatus(503)->assertJson(['status' => 'error']);
    }

    public function test_the_log_driver_masks_the_address(): void
    {
        Log::shouldReceive('info')->once()->withArgs(function (string $message, array $context): bool {
            return $message === 'newsletter.subscribed'
                && $context['email'] === 'r***@example.com'
                && $context['segment'] === 'engineering'
                && $context['locale'] === 'en';
        });

        (new LogNewsletterDriver)->subscribe('reader@example.com', 'engineering', 'en');
    }

    public function test_the_brevo_driver_posts_a_double_opt_in_request(): void
    {
        config()->set('newsletter.brevo.key', 'brevo-test-key');
        config()->set('newsletter.brevo.doi_template_id', 42);
        config()->set('newsletter.brevo.lists.local-business', 7);

        Http::fake(['api.brevo.com/*' => Http::response(null, 201)]);

        (new BrevoNewsletterDriver)->subscribe('merchant@example.com', 'local-business', 'fr');

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.brevo.com/v3/contacts/doubleOptinConfirmation'
                && $request->hasHeader('api-key', 'brevo-test-key')
                && $request['email'] === 'merchant@example.com'
                && $request['includeListIds'] === [7]
                && $request['templateId'] === 42
                && str_ends_with((string) $request['redirectionUrl'], '/fr/newsletter/confirmed')
                && $request['attributes'] === ['LOCALE' => 'fr', 'SEGMENT' => 'local-business'];
        });
    }

    public function test_the_brevo_driver_without_a_key_is_a_graceful_no_op(): void
    {
        config()->set('newsletter.brevo.key', null);

        Http::fake();

        (new BrevoNewsletterDriver)->subscribe('merchant@example.com', 'local-business', 'en');

        Http::assertNothingSent();
    }

    public function test_a_brevo_error_becomes_a_delivery_exception(): void
    {
        config()->set('newsletter.brevo.key', 'brevo-test-key');
        config()->set('newsletter.brevo.doi_template_id', 42);
        config()->set('newsletter.brevo.lists.engineering', 3);

        Http::fake(['api.brevo.com/*' => Http::response(['message' => 'invalid'], 400)]);

        $this->expectException(NewsletterDeliveryException::class);

        (new BrevoNewsletterDriver)->subscribe('reader@example.com', 'engineering', 'en');
    }
}
