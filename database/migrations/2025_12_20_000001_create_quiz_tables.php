<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng chứa các đề thi
        Schema::create('quiz_exams', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên đề thi
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['practice', 'mixed'])->default('practice'); // practice = đề cố định, mixed = đề trộn
            $table->integer('time_limit')->nullable(); // Thời gian làm bài (phút)
            $table->integer('total_questions')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Bảng chứa các chủ đề câu hỏi
        Schema::create('quiz_topics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Bảng chứa câu hỏi trắc nghiệm
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->nullable()->constrained('quiz_exams')->onDelete('cascade');
            $table->foreignId('topic_id')->nullable()->constrained('quiz_topics')->onDelete('set null');
            $table->text('question'); // Nội dung câu hỏi
            $table->json('options'); // Các đáp án (array of strings)
            $table->string('correct_answer'); // Đáp án đúng (a, b, c, d)
            $table->text('explanation')->nullable(); // Giải thích đáp án
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Bảng chứa lý thuyết
        Schema::create('quiz_theories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->nullable()->constrained('quiz_topics')->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content'); // Nội dung lý thuyết (HTML/Markdown)
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Bảng lưu kết quả thi của user
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('account')->onDelete('cascade');
            $table->foreignId('exam_id')->nullable()->constrained('quiz_exams')->onDelete('set null');
            $table->string('exam_name'); // Lưu tên đề để tra cứu khi đề bị xóa
            $table->integer('total_questions');
            $table->integer('correct_answers');
            $table->integer('wrong_answers');
            $table->decimal('score', 5, 2); // Điểm phần trăm
            $table->integer('time_spent')->nullable(); // Thời gian làm bài (giây)
            $table->json('answers')->nullable(); // Chi tiết câu trả lời
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Bảng lưu chi tiết từng câu trả lời
        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('quiz_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('quiz_questions')->onDelete('cascade');
            $table->string('user_answer')->nullable();
            $table->string('correct_answer');
            $table->boolean('is_correct');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_theories');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quiz_topics');
        Schema::dropIfExists('quiz_exams');
    }
};
