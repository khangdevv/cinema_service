import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import { examService } from '../../services/examService'
import {
    BookOpenIcon,
    ClockIcon,
    DocumentTextIcon,
    ChartBarIcon,
    SparklesIcon,
    FireIcon
} from '@heroicons/react/24/outline'

const Dashboard = () => {
    const [exams, setExams] = useState([])
    const [loading, setLoading] = useState(true)
    const [error, setError] = useState('')

    useEffect(() => {
        loadExams()
    }, [])

    const loadExams = async () => {
        try {
            const response = await examService.getExams()
            if (response.success) {
                setExams(response.data)
            }
        } catch (err) {
            setError('Không thể tải danh sách đề thi')
            console.error(err)
        } finally {
            setLoading(false)
        }
    }

    const getExamIcon = (type) => {
        return type === 'mixed' ? SparklesIcon : DocumentTextIcon
    }

    const getExamColor = (index) => {
        const colors = [
            'from-blue-500 to-indigo-600',
            'from-purple-500 to-pink-600',
            'from-green-500 to-emerald-600',
            'from-orange-500 to-red-600'
        ]
        return colors[index % colors.length]
    }

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-[60vh]">
                <div className="spinner w-16 h-16"></div>
            </div>
        )
    }

    if (error) {
        return (
            <div className="text-center py-12">
                <p className="text-error">{error}</p>
            </div>
        )
    }

    return (
        <div className="space-y-8 animate-fade-in">
            {/* Welcome */}
            <div className="card text-center">
                <h1 className="text-4xl font-bold text-gradient mb-4">
                    Chào mừng đến với Hệ Thống Ôn Tập! 🎓
                </h1>
                <p className="text-lg text-gray-600">
                    Chọn một đề thi để bắt đầu ôn tập hoặc xem lý thuyết
                </p>
            </div>

            {/* Quick actions */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <Link
                    to="/theory"
                    className="card group cursor-pointer hover:shadow-2xl"
                >
                    <div className="flex items-center space-x-4">
                        <div className="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl">
                            <BookOpenIcon className="w-8 h-8 text-white" />
                        </div>
                        <div className="flex-1">
                            <h3 className="text-lg font-bold text-gray-900 group-hover:text-primary-600">
                                Xem Lý Thuyết
                            </h3>
                            <p className="text-sm text-gray-600">
                                Tổng hợp kiến thức đầy đủ
                            </p>
                        </div>
                    </div>
                </Link>

                <Link
                    to="/statistics"
                    className="card group cursor-pointer hover:shadow-2xl"
                >
                    <div className="flex items-center space-x-4">
                        <div className="p-3 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl">
                            <ChartBarIcon className="w-8 h-8 text-white" />
                        </div>
                        <div className="flex-1">
                            <h3 className="text-lg font-bold text-gray-900 group-hover:text-primary-600">
                                Xem Thống Kê
                            </h3>
                            <p className="text-sm text-gray-600">
                                Theo dõi tiến độ của bạn
                            </p>
                        </div>
                    </div>
                </Link>
            </div>

            {/* Exams grid */}
            <div>
                <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <FireIcon className="w-7 h-7 text-orange-500 mr-2" />
                    Các Đề Trắc Nghiệm
                </h2>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                    {exams.map((exam, index) => {
                        const Icon = getExamIcon(exam.type)
                        const gradientColor = getExamColor(index)
                        const isMixed = exam.type === 'mixed'

                        return (
                            <Link
                                key={exam.id}
                                to={`/exam/${exam.slug}`}
                                className={`card group cursor-pointer relative overflow-hidden ${isMixed ? 'md:col-span-2 ring-2 ring-primary-300' : ''
                                    }`}
                            >
                                {/* Badge for mixed exam */}
                                {isMixed && (
                                    <div className="absolute top-4 right-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg flex items-center space-x-1">
                                        <SparklesIcon className="w-4 h-4" />
                                        <span>ĐỀ ĐẶC BIỆT</span>
                                    </div>
                                )}

                                <div className="flex items-start space-x-4">
                                    {/* Icon */}
                                    <div className={`p-4 bg-gradient-to-br ${gradientColor} rounded-xl shadow-lg group-hover:scale-110 transition-transform`}>
                                        <Icon className="w-8 h-8 text-white" />
                                    </div>

                                    {/* Content */}
                                    <div className="flex-1">
                                        <h3 className="text-xl font-bold text-gray-900 group-hover:text-primary-600 mb-2">
                                            {exam.name}
                                        </h3>
                                        <p className="text-gray-600 text-sm mb-4 line-clamp-2">
                                            {exam.description}
                                        </p>

                                        {/* Meta info */}
                                        <div className="flex flex-wrap gap-4 text-sm">
                                            <div className="flex items-center text-gray-600">
                                                <DocumentTextIcon className="w-4 h-4 mr-1" />
                                                <span>
                                                    {exam.questions_count || exam.total_questions || 0} câu hỏi
                                                </span>
                                            </div>

                                            {exam.time_limit && (
                                                <div className="flex items-center text-gray-600">
                                                    <ClockIcon className="w-4 h-4 mr-1" />
                                                    <span>{exam.time_limit} phút</span>
                                                </div>
                                            )}
                                        </div>

                                        {/* CTA */}
                                        <div className="mt-4">
                                            <span className="inline-flex items-center text-primary-600 font-medium group-hover:translate-x-2 transition-transform">
                                                Bắt đầu làm bài
                                                <svg className="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        )
                    })}
                </div>
            </div>

            {/* Tips */}
            <div className="card bg-gradient-to-r from-primary-50 to-purple-50">
                <h3 className="text-lg font-bold text-gray-900 mb-3">💡 Mẹo Ôn Tập</h3>
                <ul className="space-y-2 text-gray-700">
                    <li className="flex items-start">
                        <span className="text-primary-500 mr-2">•</span>
                        <span>Đọc kỹ lý thuyết trước khi làm bài</span>
                    </li>
                    <li className="flex items-start">
                        <span className="text-primary-500 mr-2">•</span>
                        <span>Làm từng đề riêng trước khi làm đề trộn</span>
                    </li>
                    <li className="flex items-start">
                        <span className="text-primary-500 mr-2">•</span>
                        <span>Review kỹ phần giải thích sau mỗi bài thi</span>
                    </li>
                    <li className="flex items-start">
                        <span className="text-primary-500 mr-2">•</span>
                        <span>Theo dõi thống kê để biết điểm yếu của bạn</span>
                    </li>
                </ul>
            </div>
        </div>
    )
}

export default Dashboard
