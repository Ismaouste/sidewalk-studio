<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * The first message can say what kind of project it is.
 *
 * Three optional qualification fields — project type, budget band, timeline —
 * ride the contact form into the composed message. They are optional because
 * a recruiter or a reader with a question should not be interrogated about a
 * budget they do not have; they exist because a merchant with a project
 * should not need a second email to say the three things every scoping call
 * starts with.
 */
class ContactQualificationTest extends TestCase
{
    public function test_qualification_fields_reach_the_message(): void
    {
        $response = $this->post('/en/contact', [
            'name' => 'Ada',
            'email' => 'ada@example.com',
            'summary' => 'A message long enough to pass the minimum length rule.',
            'project_type' => 'E-commerce build',
            'budget' => '€3–8k',
            'timeline' => 'This quarter',
        ]);

        $response->assertRedirect();

        $location = (string) $response->headers->get('Location');

        $this->assertStringContainsString(rawurlencode('E-commerce build'), $location);
        $this->assertStringContainsString(rawurlencode('€3–8k'), $location);
        $this->assertStringContainsString(rawurlencode('This quarter'), $location);
    }

    public function test_the_fields_stay_optional(): void
    {
        $response = $this->post('/en/contact', [
            'name' => 'Ada',
            'email' => 'ada@example.com',
            'summary' => 'A message long enough to pass the minimum length rule.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }
}
