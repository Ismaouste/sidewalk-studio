<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One answer per question, per language.
 *
 * The questions themselves are declared in code, not stored — they are part
 * of the site's design the same way a page's slots are, and an operator who
 * could invent a question could invent one the layout has nowhere to put. So
 * the table holds only what the operator writes.
 *
 * There is no `required` here and there is no default. An unanswered question
 * is a legitimate state that the public page renders by rendering nothing:
 * the marginalia slot beside a spread simply stays closed. That is what makes
 * this safe to ship before a single question has been answered.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_answers', function (Blueprint $table): void {
            $table->id();
            $table->string('question_key');
            $table->string('locale', 5);
            $table->text('answer');
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['question_key', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_answers');
    }
};
