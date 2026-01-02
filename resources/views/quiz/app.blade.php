<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ôn tập Mã nguồn mở - STU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .correct-answer { background-color: #dcfce7 !important; border-color: #22c55e !important; }
        .wrong-answer { background-color: #fee2e2 !important; border-color: #ef4444 !important; }
        .selected-answer { border-color: #6366f1 !important; border-width: 2px !important; }

        /* Loading spinner */
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #6366f1;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Sidebar */
        .sidebar-item {
            transition: all 0.2s ease;
        }
        .sidebar-item:hover {
            background-color: #f3f4f6;
        }
        .sidebar-item.active {
            background-color: #eef2ff;
            color: #4f46e5;
            border-right: 3px solid #4f46e5;
        }

        /* Question number badges */
        .question-badge {
            transition: all 0.2s ease;
        }
        .question-badge.answered {
            background-color: #4f46e5;
            color: white;
        }
        .question-badge.current {
            border: 2px solid #4f46e5;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #a1a1a1; }

        /* Theory content styles */
        .theory-content h1 { font-size: 1.75rem; font-weight: 700; margin-top: 1.5rem; margin-bottom: 1rem; color: #1f2937; }
        .theory-content h2 { font-size: 1.5rem; font-weight: 600; margin-top: 1.25rem; margin-bottom: 0.75rem; color: #374151; }
        .theory-content h3 { font-size: 1.25rem; font-weight: 600; margin-top: 1rem; margin-bottom: 0.5rem; color: #4b5563; }
        .theory-content p { margin-bottom: 0.75rem; line-height: 1.7; }
        .theory-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
        .theory-content ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1rem; }
        .theory-content li { margin-bottom: 0.25rem; }
        .theory-content code { background-color: #f3f4f6; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-family: monospace; font-size: 0.875rem; }
        .theory-content pre { background-color: #1f2937; color: #e5e7eb; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; margin-bottom: 1rem; }
        .theory-content pre code { background-color: transparent; padding: 0; color: inherit; }
        .theory-content table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        .theory-content th, .theory-content td { border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; text-align: left; }
        .theory-content th { background-color: #f9fafb; font-weight: 600; }
    </style>
</head>
<body class="bg-gray-50">
    <div id="app"></div>

    <script>
        // Global state
        const App = {
            user: null,
            currentPage: 'home',
            exams: [],
            currentExam: null,
            currentQuestions: [],
            currentAnswers: {},
            currentQuestionIndex: 0,
            attemptId: null,
            startTime: null,
            topics: [],
            theories: [],
            statistics: null,
            history: [],
            progress: null,
            examResult: null,
        };

        // API helper
        async function api(endpoint, options = {}) {
            const response = await fetch(`/quiz/api${endpoint}`, {
                ...options,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    ...options.headers,
                },
            });
            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = '/quiz/login';
                }
                throw new Error('API Error');
            }
            return response.json();
        }

        // Initialize app
        async function init() {
            try {
                const data = await api('/me');
                App.user = data.user;
                render();

                // Check URL and navigate
                const path = window.location.pathname.replace('/quiz', '');
                if (path.startsWith('/exam/')) {
                    const slug = path.replace('/exam/', '');
                    await loadExam(slug);
                } else if (path === '/statistics') {
                    await loadStatistics();
                } else if (path === '/theory') {
                    await loadTheories();
                } else if (path === '/history') {
                    await loadHistory();
                } else {
                    await loadExams();
                }
            } catch (e) {
                window.location.href = '/quiz/login';
            }
        }

        // Navigation
        function navigate(page, data = null) {
            App.currentPage = page;
            if (page === 'home') {
                history.pushState({}, '', '/quiz');
                loadExams();
            } else if (page === 'exam' && data) {
                history.pushState({}, '', `/quiz/exam/${data}`);
                loadExam(data);
            } else if (page === 'statistics') {
                history.pushState({}, '', '/quiz/statistics');
                loadStatistics();
            } else if (page === 'theory') {
                history.pushState({}, '', '/quiz/theory');
                loadTheories();
            } else if (page === 'history') {
                history.pushState({}, '', '/quiz/history');
                loadHistory();
            }
        }

        // Load functions
        async function loadExams() {
            App.currentPage = 'home';
            render();
            const data = await api('/exams');
            App.exams = data.exams;
            render();
        }

        async function loadExam(slug) {
            App.currentPage = 'exam';
            App.currentAnswers = {};
            App.currentQuestionIndex = 0;
            App.examResult = null;
            render();

            const data = await api(`/exams/${slug}`);
            App.currentExam = data.exam;
            App.currentQuestions = data.questions;

            // Start attempt
            const startData = await api(`/exams/${slug}/start`, { method: 'POST' });
            App.attemptId = startData.attempt_id;
            App.startTime = new Date();

            render();
        }

        async function loadStatistics() {
            App.currentPage = 'statistics';
            render();

            const [overview, progress] = await Promise.all([
                api('/statistics/overview'),
                api('/statistics/progress')
            ]);

            App.statistics = overview;
            App.progress = progress;
            render();

            // Draw charts after render
            setTimeout(drawCharts, 100);
        }

        async function loadHistory() {
            App.currentPage = 'history';
            render();

            const data = await api('/statistics/history?per_page=20');
            App.history = data.data;
            render();
        }

        async function loadTheories() {
            App.currentPage = 'theory';
            render();

            const data = await api('/theories');
            App.topics = data.topics;
            render();
        }

        // Submit exam
        async function submitExam() {
            if (!confirm('Bạn có chắc muốn nộp bài?')) return;

            const timeSpent = Math.floor((new Date() - App.startTime) / 1000);

            const data = await api(`/exams/${App.currentExam.slug}/submit`, {
                method: 'POST',
                body: JSON.stringify({
                    attempt_id: App.attemptId,
                    answers: App.currentAnswers,
                    time_spent: timeSpent
                })
            });

            App.examResult = data;
            App.currentPage = 'result';
            render();
        }

        // Select answer
        function selectAnswer(questionId, answer) {
            App.currentAnswers[questionId] = answer;
            render();
        }

        // Draw charts
        function drawCharts() {
            if (!App.progress || !App.progress.attempts.length) return;

            // Score progress chart
            const scoreCtx = document.getElementById('scoreChart');
            if (scoreCtx) {
                new Chart(scoreCtx, {
                    type: 'line',
                    data: {
                        labels: App.progress.attempts.map((a, i) => `Lần ${i + 1}`),
                        datasets: [{
                            label: 'Điểm số (%)',
                            data: App.progress.attempts.map(a => a.score),
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { min: 0, max: 100 } }
                    }
                });
            }

            // Topic accuracy chart
            const topicCtx = document.getElementById('topicChart');
            if (topicCtx && App.progress.topic_accuracy.length) {
                new Chart(topicCtx, {
                    type: 'bar',
                    data: {
                        labels: App.progress.topic_accuracy.map(t => t.topic_name.substring(0, 15)),
                        datasets: [{
                            label: 'Độ chính xác (%)',
                            data: App.progress.topic_accuracy.map(t => t.accuracy),
                            backgroundColor: '#6366f1'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { min: 0, max: 100 } }
                    }
                });
            }
        }

        // Format time
        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }

        // Render function
        function render() {
            const app = document.getElementById('app');

            app.innerHTML = `
                <div class="min-h-screen flex">
                    <!-- Sidebar -->
                    <div class="w-64 bg-white border-r border-gray-200 fixed h-full">
                        <div class="p-6">
                            <h1 class="text-xl font-bold gradient-text">Ôn tập PMNM</h1>
                            <p class="text-gray-500 text-sm mt-1">STU 2025</p>
                        </div>

                        <nav class="mt-4">
                            <a href="#" onclick="navigate('home'); return false;"
                               class="sidebar-item flex items-center px-6 py-3 text-gray-700 ${App.currentPage === 'home' ? 'active' : ''}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Đề thi
                            </a>
                            <a href="#" onclick="navigate('theory'); return false;"
                               class="sidebar-item flex items-center px-6 py-3 text-gray-700 ${App.currentPage === 'theory' ? 'active' : ''}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Lý thuyết
                            </a>
                            <a href="#" onclick="navigate('statistics'); return false;"
                               class="sidebar-item flex items-center px-6 py-3 text-gray-700 ${App.currentPage === 'statistics' ? 'active' : ''}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Thống kê
                            </a>
                            <a href="#" onclick="navigate('history'); return false;"
                               class="sidebar-item flex items-center px-6 py-3 text-gray-700 ${App.currentPage === 'history' ? 'active' : ''}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Lịch sử
                            </a>
                        </nav>

                        <!-- User info -->
                        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
                            <div class="flex items-center">
                                <img src="${App.user?.avatar || 'https://ui-avatars.com/api/?name=User'}"
                                     class="w-10 h-10 rounded-full mr-3" alt="">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">${App.user?.name || 'User'}</p>
                                    <p class="text-xs text-gray-500 truncate">${App.user?.email || ''}</p>
                                </div>
                                <form action="/quiz/logout" method="POST" class="ml-2">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                    <button type="submit" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Main content -->
                    <div class="flex-1 ml-64">
                        ${renderContent()}
                    </div>
                </div>
            `;
        }

        function renderContent() {
            switch(App.currentPage) {
                case 'home': return renderHome();
                case 'exam': return renderExam();
                case 'result': return renderResult();
                case 'statistics': return renderStatistics();
                case 'history': return renderHistory();
                case 'theory': return renderTheory();
                default: return renderHome();
            }
        }

        function renderHome() {
            if (!App.exams.length) {
                return `<div class="flex items-center justify-center h-screen"><div class="spinner"></div></div>`;
            }

            return `
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Chọn đề thi</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        ${App.exams.map(exam => `
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer"
                                 onclick="navigate('exam', '${exam.slug}')">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="p-3 bg-indigo-100 rounded-lg">
                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    ${exam.type === 'mixed' ? `<span class="px-2 py-1 bg-purple-100 text-purple-600 text-xs font-medium rounded-full">Trộn đề</span>` : ''}
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">${exam.name}</h3>
                                <p class="text-gray-500 text-sm mb-4">${exam.description || ''}</p>
                                <div class="flex items-center text-sm text-gray-500">
                                    <span class="flex items-center mr-4">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        ${exam.questions_count || exam.total_questions} câu
                                    </span>
                                    ${exam.time_limit ? `
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            ${exam.time_limit} phút
                                        </span>
                                    ` : ''}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        function renderExam() {
            if (!App.currentQuestions.length) {
                return `<div class="flex items-center justify-center h-screen"><div class="spinner"></div></div>`;
            }

            const current = App.currentQuestions[App.currentQuestionIndex];
            const answeredCount = Object.keys(App.currentAnswers).length;

            return `
                <div class="flex h-screen">
                    <!-- Question panel -->
                    <div class="flex-1 p-8 overflow-auto">
                        <div class="max-w-3xl mx-auto">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-bold text-gray-800">${App.currentExam.name}</h2>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-500">${answeredCount}/${App.currentQuestions.length} đã trả lời</span>
                                    <button onclick="submitExam()"
                                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                        Nộp bài
                                    </button>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <div class="flex items-center mb-4">
                                    <span class="flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full font-semibold text-sm mr-3">
                                        ${current.index}
                                    </span>
                                    <div class="text-lg text-gray-800 flex-1" style="white-space: pre-wrap;">${current.question}</div>
                                </div>

                                <div class="space-y-3 mt-6">
                                    ${['a', 'b', 'c', 'd'].map(key => current.options[key] ? `
                                        <div onclick="selectAnswer(${current.id}, '${key}')"
                                             class="p-4 rounded-lg border-2 cursor-pointer transition-all
                                                    ${App.currentAnswers[current.id] === key ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'}">
                                            <div class="flex items-start">
                                                <span class="flex items-center justify-center w-6 h-6 rounded-full border-2 mr-3 flex-shrink-0 mt-0.5
                                                            ${App.currentAnswers[current.id] === key ? 'border-indigo-500 bg-indigo-500 text-white' : 'border-gray-300'}">
                                                    ${key.toUpperCase()}
                                                </span>
                                                <span class="text-gray-700" style="white-space: pre-wrap;">${current.options[key]}</span>
                                            </div>
                                        </div>
                                    ` : '').join('')}
                                </div>
                            </div>

                            <!-- Navigation -->
                            <div class="flex justify-between mt-6">
                                <button onclick="App.currentQuestionIndex = Math.max(0, App.currentQuestionIndex - 1); render();"
                                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors ${App.currentQuestionIndex === 0 ? 'opacity-50 cursor-not-allowed' : ''}"
                                        ${App.currentQuestionIndex === 0 ? 'disabled' : ''}>
                                    ← Câu trước
                                </button>
                                <button onclick="App.currentQuestionIndex = Math.min(App.currentQuestions.length - 1, App.currentQuestionIndex + 1); render();"
                                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors ${App.currentQuestionIndex === App.currentQuestions.length - 1 ? 'opacity-50 cursor-not-allowed' : ''}"
                                        ${App.currentQuestionIndex === App.currentQuestions.length - 1 ? 'disabled' : ''}>
                                    Câu sau →
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Question navigator -->
                    <div class="w-72 bg-white border-l border-gray-200 p-4 overflow-auto">
                        <h3 class="font-semibold text-gray-700 mb-4">Danh sách câu hỏi</h3>
                        <div class="grid grid-cols-5 gap-2">
                            ${App.currentQuestions.map((q, i) => `
                                <button onclick="App.currentQuestionIndex = ${i}; render();"
                                        class="question-badge w-10 h-10 rounded-lg border-2 flex items-center justify-center text-sm font-medium
                                               ${App.currentAnswers[q.id] ? 'answered' : 'border-gray-200'}
                                               ${App.currentQuestionIndex === i ? 'current' : ''}">
                                    ${i + 1}
                                </button>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
        }

        function renderResult() {
            if (!App.examResult) return '';

            const summary = App.examResult.summary;
            const results = App.examResult.results;

            return `
                <div class="p-8">
                    <div class="max-w-4xl mx-auto">
                        <!-- Summary -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6">Kết quả làm bài</h2>
                            <div class="grid grid-cols-4 gap-4">
                                <div class="text-center p-4 bg-indigo-50 rounded-lg">
                                    <p class="text-3xl font-bold text-indigo-600">${summary.score}%</p>
                                    <p class="text-sm text-gray-600">Điểm số</p>
                                </div>
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <p class="text-3xl font-bold text-green-600">${summary.correct_answers}</p>
                                    <p class="text-sm text-gray-600">Câu đúng</p>
                                </div>
                                <div class="text-center p-4 bg-red-50 rounded-lg">
                                    <p class="text-3xl font-bold text-red-600">${summary.wrong_answers}</p>
                                    <p class="text-sm text-gray-600">Câu sai</p>
                                </div>
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <p class="text-3xl font-bold text-gray-600">${formatTime(summary.time_spent)}</p>
                                    <p class="text-sm text-gray-600">Thời gian</p>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-center space-x-4">
                                <button onclick="navigate('home')" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    Về trang chủ
                                </button>
                                <button onclick="navigate('statistics')" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Xem thống kê
                                </button>
                            </div>
                        </div>

                        <!-- Detailed results -->
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Chi tiết câu trả lời</h3>
                        <div class="space-y-4">
                            ${results.map((r, i) => `
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 ${r.is_correct ? 'border-l-4 border-l-green-500' : 'border-l-4 border-l-red-500'}">
                                    <div class="flex items-start mb-4">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-full font-semibold text-sm mr-3 ${r.is_correct ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'}">
                                            ${i + 1}
                                        </span>
                                        <div class="flex-1">
                                            <p class="text-gray-800 font-medium" style="white-space: pre-wrap;">${r.question}</p>
                                        </div>
                                        ${r.is_correct ?
                                            '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                                            '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                                        }
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 mb-4">
                                        ${['a', 'b', 'c', 'd'].map(key => r.options[key] ? `
                                            <div class="p-3 rounded-lg ${
                                                key === r.correct_answer ? 'correct-answer' :
                                                key === r.user_answer && !r.is_correct ? 'wrong-answer' : 'bg-gray-50'
                                            }">
                                                <span class="font-medium">${key.toUpperCase()}.</span> ${r.options[key]}
                                            </div>
                                        ` : '').join('')}
                                    </div>

                                    ${r.explanation ? `
                                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                                            <p class="text-sm font-medium text-blue-800 mb-1">Giải thích:</p>
                                            <p class="text-sm text-blue-700">${r.explanation}</p>
                                        </div>
                                    ` : ''}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
        }

        function renderStatistics() {
            if (!App.statistics) {
                return `<div class="flex items-center justify-center h-screen"><div class="spinner"></div></div>`;
            }

            const overview = App.statistics.overview;
            const examStats = App.statistics.exam_stats;

            return `
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Thống kê học tập</h2>

                    <!-- Overview cards -->
                    <div class="grid grid-cols-4 gap-4 mb-8">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <p class="text-sm text-gray-500 mb-1">Tổng số lần làm</p>
                            <p class="text-3xl font-bold text-gray-800">${overview.total_attempts}</p>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <p class="text-sm text-gray-500 mb-1">Điểm trung bình</p>
                            <p class="text-3xl font-bold text-indigo-600">${overview.avg_score}%</p>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <p class="text-sm text-gray-500 mb-1">Điểm cao nhất</p>
                            <p class="text-3xl font-bold text-green-600">${overview.best_score}%</p>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <p class="text-sm text-gray-500 mb-1">Độ chính xác</p>
                            <p class="text-3xl font-bold text-purple-600">${overview.accuracy}%</p>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tiến độ điểm số</h3>
                            <canvas id="scoreChart"></canvas>
                        </div>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Độ chính xác theo chủ đề</h3>
                            <canvas id="topicChart"></canvas>
                        </div>
                    </div>

                    <!-- Stats by exam -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Thống kê theo đề thi</h3>
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-3 px-4">Đề thi</th>
                                    <th class="text-center py-3 px-4">Số lần làm</th>
                                    <th class="text-center py-3 px-4">Điểm TB</th>
                                    <th class="text-center py-3 px-4">Điểm cao nhất</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${examStats.map(stat => `
                                    <tr class="border-b">
                                        <td class="py-3 px-4 font-medium">${stat.exam_name}</td>
                                        <td class="text-center py-3 px-4">${stat.attempts}</td>
                                        <td class="text-center py-3 px-4">${parseFloat(stat.avg_score).toFixed(1)}%</td>
                                        <td class="text-center py-3 px-4 text-green-600 font-semibold">${stat.best_score}%</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        }

        function renderHistory() {
            return `
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Lịch sử làm bài</h2>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left py-4 px-6">Đề thi</th>
                                    <th class="text-center py-4 px-6">Điểm</th>
                                    <th class="text-center py-4 px-6">Đúng/Sai</th>
                                    <th class="text-center py-4 px-6">Thời gian</th>
                                    <th class="text-center py-4 px-6">Ngày làm</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${App.history.map(item => `
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="py-4 px-6 font-medium">${item.exam_name}</td>
                                        <td class="text-center py-4 px-6">
                                            <span class="px-3 py-1 rounded-full text-sm font-medium ${
                                                item.score >= 80 ? 'bg-green-100 text-green-700' :
                                                item.score >= 50 ? 'bg-yellow-100 text-yellow-700' :
                                                'bg-red-100 text-red-700'
                                            }">${item.score}%</span>
                                        </td>
                                        <td class="text-center py-4 px-6">
                                            <span class="text-green-600">${item.correct_answers}</span> /
                                            <span class="text-red-600">${item.wrong_answers}</span>
                                        </td>
                                        <td class="text-center py-4 px-6">${formatTime(item.time_spent || 0)}</td>
                                        <td class="text-center py-4 px-6 text-gray-500">${new Date(item.completed_at).toLocaleDateString('vi-VN')}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                        ${App.history.length === 0 ? `
                            <div class="text-center py-12 text-gray-500">
                                Chưa có lịch sử làm bài nào
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }

        function renderTheory() {
            if (!App.topics.length) {
                return `<div class="flex items-center justify-center h-screen"><div class="spinner"></div></div>`;
            }

            return `
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Tài liệu lý thuyết</h2>

                    <div class="space-y-6">
                        ${App.topics.map(topic => `
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="p-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                                    <h3 class="text-xl font-bold">${topic.name}</h3>
                                    ${topic.description ? `<p class="mt-1 opacity-90">${topic.description}</p>` : ''}
                                </div>

                                ${topic.theories && topic.theories.length ? `
                                    <div class="divide-y">
                                        ${topic.theories.map(theory => `
                                            <details class="group">
                                                <summary class="p-4 cursor-pointer hover:bg-gray-50 flex items-center justify-between">
                                                    <span class="font-medium text-gray-800">${theory.title}</span>
                                                    <svg class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </summary>
                                                <div class="px-6 pb-6 theory-content">
                                                    ${theory.content}
                                                </div>
                                            </details>
                                        `).join('')}
                                    </div>
                                ` : `
                                    <div class="p-6 text-gray-500 text-center">
                                        Chưa có nội dung lý thuyết
                                    </div>
                                `}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        // Start app
        init();
    </script>
</body>
</html>
