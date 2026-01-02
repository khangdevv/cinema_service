import { useState, useEffect, useRef } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { examService } from '../../services/examService'
import {
    ClockIcon,
    CheckCircleIcon,
    XCircleIcon
} from '@heroicons/react/24/outline'

const ExamPage = () => {
    const { slug } = useParams()
    const navigate = useNavigate()

    const [exam, setExam] = useState(null)
    const [questions, setQuestions] = useState([])
    const [answers, setAnswers] = useState({})
    const [currentQuestion, setCurrentQuestion] = useState(0)
    const [timeLeft, setTimeLeft] = useState(0)
    const [loading, setLoading] = useState(true)
    const [submitting, setSubmitting] = useState(false)
    const [showConfirm, setShowConfirm] = useState(false)

    const timerRef = useRef(null)
    const startTimeRef = useRef(Date.now())

    useEffect(() => {
        loadExam()
        return () => {
            if (timerRef.current) clearInterval(timerRef.current)
        }
    }, [slug])

    const loadExam = async () => {
        try {
            const response = await examService.getExam(slug)
            if (response.success) {
                setExam(response.data)
                setQuestions(response.questions)
                setTimeLeft(response.data.time_limit * 60) // Convert to seconds
                startTimer()
            }
        } catch (error) {
            console.error(error)
            alert('Không thể tải đề thi')
            navigate('/')
        } finally {
            setLoading(false)
        }
    }

    const startTimer = () => {
        timerRef.current = setInterval(() => {
            setTimeLeft((prev) => {
                if (prev <= 1) {
                    handleSubmit(true) // Auto submit when time's up
                    return 0
                }
                return prev - 1
            })
        }, 1000)
    }

    const selectAnswer = (questionId, answer) => {
        setAnswers({
            ...answers,
            [questionId]: answer
        })
    }

    const formatTime = (seconds) => {
        const mins = Math.floor(seconds / 60)
        const secs = seconds % 60
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
    }

    const getTimeSpent = () => {
        return Math.floor((Date.now() - startTimeRef.current) / 1000)
    }

    const handleSubmit = async (autoSubmit = false) => {
        // Always allow submission, just show warning in modal
        setSubmitting(true)
        setShowConfirm(false) // Close modal

        try {
            const timeSpent = getTimeSpent()
            const response = await examService.submitExam(slug, answers, timeSpent)

            if (response.success) {
                navigate(`/result/${response.data.attempt_id}`)
            } else {
                alert('Có lỗi khi nộp bài: ' + (response.message || 'Unknown error'))
            }
        } catch (error) {
            console.error(error)
            alert('Có lỗi xảy ra khi nộp bài: ' + (error.response?.data?.message || error.message))
        } finally {
            setSubmitting(false)
        }
    }

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-[60vh]">
                <div className="spinner w-16 h-16"></div>
            </div>
        )
    }

    const currentQ = questions[currentQuestion]
    const answeredCount = Object.keys(answers).length
    const progress = (answeredCount / questions.length) * 100

    return (
        <div className="max-w-4xl mx-auto space-y-6 animate-fade-in">
            {/* Header */}
            <div className="card">
                <div className="flex items-center justify-between mb-4">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900">{exam?.name}</h1>
                        <p className="text-sm text-gray-600 mt-1">
                            Câu {currentQuestion + 1} / {questions.length}
                        </p>
                    </div>

                    {/* Timer */}
                    <div className={`flex items-center space-x-2 px-4 py-2 rounded-lg ${timeLeft < 300 ? 'bg-error/10 text-error' : 'bg-primary-50 text-primary-700'
                        }`}>
                        <ClockIcon className="w-5 h-5" />
                        <span className="text-lg font-bold">{formatTime(timeLeft)}</span>
                    </div>
                </div>

                {/* Progress bar */}
                <div className="w-full bg-gray-200 rounded-full h-2">
                    <div
                        className="bg-gradient-to-r from-primary-500 to-purple-600 h-2 rounded-full transition-all duration-300"
                        style={{ width: `${progress}%` }}
                    />
                </div>
                <p className="text-sm text-gray-600 mt-2">
                    Đã trả lời: {answeredCount} / {questions.length} câu
                </p>
            </div>

            {/* Question */}
            <div className="card">
                <div className="mb-6">
                    <div className="flex items-start space-x-2 mb-4">
                        <span className="flex-shrink-0 w-8 h-8 rounded-full bg-primary-500 text-white flex items-center justify-center font-bold text-sm">
                            {currentQuestion + 1}
                        </span>
                        <p className="text-lg text-gray-900 flex-1 leading-relaxed">
                            {currentQ?.question}
                        </p>
                    </div>

                    {/* Options */}
                    <div className="space-y-3 mt-6">
                        {['a', 'b', 'c', 'd'].map((option) => {
                            if (!currentQ?.options?.[option]) return null

                            const isSelected = answers[currentQ.id] === option

                            return (
                                <button
                                    key={option}
                                    onClick={() => selectAnswer(currentQ.id, option)}
                                    className={`option w-full text-left ${isSelected ? 'option-selected' : ''
                                        }`}
                                >
                                    <div className="flex items-start space-x-3">
                                        <div className={`flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center ${isSelected
                                            ? 'border-primary-500 bg-primary-500'
                                            : 'border-gray-300'
                                            }`}>
                                            {isSelected && (
                                                <CheckCircleIcon className="w-5 h-5 text-white" />
                                            )}
                                        </div>
                                        <div className="flex-1">
                                            <span className="font-medium text-gray-700 mr-2">
                                                {option.toUpperCase()}.
                                            </span>
                                            <span className="text-gray-900">
                                                {currentQ.options[option]}
                                            </span>
                                        </div>
                                    </div>
                                </button>
                            )
                        })}
                    </div>
                </div>

                {/* Navigation */}
                <div className="flex items-center justify-between pt-6 border-t border-gray-200">
                    <button
                        onClick={() => setCurrentQuestion(prev => Math.max(0, prev - 1))}
                        disabled={currentQuestion === 0}
                        className="btn btn-secondary disabled:opacity-50"
                    >
                        ← Câu trước
                    </button>

                    <button
                        onClick={() => setShowConfirm(true)}
                        disabled={submitting}
                        className="btn btn-success"
                    >
                        {submitting ? 'Đang nộp...' : '√ Nộp bài'}
                    </button>

                    <button
                        onClick={() => setCurrentQuestion(prev => Math.min(questions.length - 1, prev + 1))}
                        disabled={currentQuestion === questions.length - 1}
                        className="btn btn-primary disabled:opacity-50"
                    >
                        Câu sau →
                    </button>
                </div>
            </div>

            {/* Question navigator */}
            <div className="card">
                <h3 className="font-bold text-gray-900 mb-4">Danh sách câu hỏi</h3>
                <div className="grid grid-cols-10 gap-2">
                    {questions.map((q, idx) => {
                        const isAnswered = answers[q.id]
                        const isCurrent = idx === currentQuestion

                        return (
                            <button
                                key={q.id}
                                onClick={() => setCurrentQuestion(idx)}
                                className={`aspect-square rounded-lg font-medium transition-all ${isCurrent
                                    ? 'bg-primary-500 text-white shadow-lg scale-110'
                                    : isAnswered
                                        ? 'bg-success/20 text-success hover:bg-success/30'
                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                    }`}
                            >
                                {idx + 1}
                            </button>
                        )
                    })}
                </div>
            </div>

            {/* Confirm modal */}
            {showConfirm && (
                <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                    <div className="card max-w-md w-full animate-bounce-in">
                        <h3 className="text-xl font-bold text-gray-900 mb-4">
                            Xác nhận nộp bài
                        </h3>
                        <p className="text-gray-600 mb-6">
                            Bạn đã trả lời {answeredCount}/{questions.length} câu.
                            {answeredCount < questions.length && (
                                <span className="text-warning block mt-2">
                                    ⚠️ Bạn còn {questions.length - answeredCount} câu chưa trả lời!
                                </span>
                            )}
                        </p>
                        <div className="flex space-x-3">
                            <button
                                onClick={() => setShowConfirm(false)}
                                className="btn btn-secondary flex-1"
                                disabled={submitting}
                            >
                                Hủy
                            </button>
                            <button
                                onClick={() => handleSubmit(false)}
                                className="btn btn-success flex-1"
                                disabled={submitting}
                            >
                                {submitting ? 'Đang nộp...' : 'Xác nhận nộp'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    )
}

export default ExamPage
