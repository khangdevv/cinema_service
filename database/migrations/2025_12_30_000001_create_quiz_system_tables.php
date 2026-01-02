<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Bảng người dùng - chỉ tạo nếu chưa tồn tại
        if (!Schema::hasTable('accounts')) {
            Schema::create('accounts', function (Blueprint $table) {
                $table->id();
                $table->string('email', 191)->unique()->nullable();
                $table->string('google_id', 191)->unique()->nullable();
                $table->string('phone', 20)->nullable();
                $table->string('password')->nullable();
                $table->string('full_name')->nullable();
                $table->string('avatar', 500)->nullable();
                $table->enum('role', ['USER', 'ADMIN'])->default('USER');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Bảng đề thi
        if (!Schema::hasTable('quiz_exams')) {
            Schema::create('quiz_exams', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug', 191)->unique();
                $table->text('description')->nullable();
                $table->enum('type', ['practice', 'mixed'])->default('practice');
                $table->integer('time_limit')->nullable();
                $table->integer('total_questions')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Bảng chủ đề
        if (!Schema::hasTable('quiz_topics')) {
            Schema::create('quiz_topics', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug', 191)->unique();
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // Bảng câu hỏi
        if (!Schema::hasTable('quiz_questions')) {
            Schema::create('quiz_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->nullable()->constrained('quiz_exams')->onDelete('cascade');
                $table->foreignId('topic_id')->nullable()->constrained('quiz_topics')->onDelete('set null');
                $table->text('question');
                $table->text('option_a');
                $table->text('option_b');
                $table->text('option_c')->nullable();
                $table->text('option_d')->nullable();
                $table->char('correct_answer', 1);
                $table->text('explanation')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // Bảng lý thuyết
        if (!Schema::hasTable('quiz_theories')) {
            Schema::create('quiz_theories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('topic_id')->nullable()->constrained('quiz_topics')->onDelete('set null');
                $table->string('title');
                $table->string('slug', 191)->unique();
                $table->longText('content');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // Bảng lịch sử làm bài
        if (!Schema::hasTable('quiz_attempts')) {
            Schema::create('quiz_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
                $table->foreignId('exam_id')->nullable()->constrained('quiz_exams')->onDelete('set null');
                $table->string('exam_name');
                $table->integer('total_questions');
                $table->integer('correct_answers')->default(0);
                $table->integer('wrong_answers')->default(0);
                $table->decimal('score', 5, 2)->default(0);
                $table->integer('time_spent')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        // Bảng chi tiết câu trả lời
        if (!Schema::hasTable('quiz_attempt_answers')) {
            Schema::create('quiz_attempt_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attempt_id')->constrained('quiz_attempts')->onDelete('cascade');
                $table->foreignId('question_id')->constrained('quiz_questions')->onDelete('cascade');
                $table->char('user_answer', 1)->nullable();
                $table->char('correct_answer', 1);
                $table->boolean('is_correct')->default(false);
                $table->timestamps();
            });
        }

        // Bảng sessions
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id', 191)->primary();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('quiz_attempt_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_theories');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quiz_topics');
        Schema::dropIfExists('quiz_exams');
        Schema::dropIfExists('accounts');
    }
};
