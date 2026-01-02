import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import { examService, statisticsService } from '../../services'
import { Line, Bar, Pie } from 'react-chartjs-2'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js'
import {
    ChartBarIcon,
    ClockIcon,
    TrophyIcon,
    FireIcon
} from '@heroicons/react/24/outline'

// Register ChartJS components
ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend
)

const StatisticsPage = () => {
    const [history, setHistory] = useState([])
    const [loading, setLoading] = useState(true)
    const [stats, setStats] = useState({
        totalAttempts: 0,
        avgScore: 0,
        highestScore: 0,
        totalTime: 0
    })

    useEffect(() => {
        loadData()
    }, [])

    const loadData = async () => {
        try {
            console.log('Loading statistics...')
            const response = await examService.getHistory()
            console.log('Statistics response:', response)

            if (response.success && response.data) {
                const data = Array.isArray(response.data) ? response.data : []
                setHistory(data)

                // Calculate stats
                if (data.length > 0) {
                    const scores = data.map(a => parseFloat(a.score) || 0)
                    const avgScore = scores.reduce((sum, score) => sum + score, 0) / data.length
                    const highestScore = Math.max(...scores)
                    const totalTime = data.reduce((sum, attempt) => sum + (attempt.time_spent || 0), 0)

                    setStats({
                        totalAttempts: data.length,
                        avgScore: avgScore.toFixed(1),
                        highestScore: highestScore.toFixed(1),
                        totalTime
                    })
                }
            } else {
                console.warn('No data in statistics response')
                setHistory([])
            }
        } catch (error) {
            console.error('Error loading statistics:', error)
            setHistory([])
        } finally {
            setLoading(false)
        }
    }

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-[60vh]">
                <div className="spinner w-16 h-16"></div>
            </div>
        )
    }

    // Prepare chart data
    const scoreHistory = history.slice(-10).reverse()

    const lineChartData = {
        labels: scoreHistory.map((_, idx) => `Lần ${scoreHistory.length - idx}`),
        datasets: [{
            label: 'Điểm số (%)',
            data: scoreHistory.map(a => a.score),
            borderColor: 'rgb(14, 165, 233)',
            backgroundColor: 'rgba(14, 165, 233, 0.1)',
            tension: 0.4,
            fill: true
        }]
    }

    const barChartData = {
        labels: scoreHistory.map((_, idx) => `Lần ${scoreHistory.length - idx}`),
        datasets: [
            {
                label: 'Câu đúng',
                data: scoreHistory.map(a => a.correct_answers),
                backgroundColor: 'rgba(16, 185, 129, 0.8)'
            },
            {
                label: 'Câu sai',
                data: scoreHistory.map(a => a.wrong_answers),
                backgroundColor: 'rgba(239, 68, 68, 0.8)'
            }
        ]
    }

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }

    return (
        <div className="space-y-6 animate-fade-in">
            {/* Header */}
            <div className="card">
                <div className="flex items-center space-x-3 mb-4">
                    <div className="p-3 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl">
                        <ChartBarIcon className="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h1 className="text-3xl font-bold text-gradient">Thống Kê</h1>
                        <p className="text-gray-600">Theo dõi tiến độ học tập của bạn</p>
                    </div>
                </div>
            </div>

            {/* Overview cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div className="card bg-gradient-to-br from-blue-50 to-indigo-50">
                    <div className="flex items-center space-x-3">
                        <div className="p-3 bg-primary-500 rounded-lg">
                            <FireIcon className="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <div className="text-3xl font-bold text-primary-600">{stats.totalAttempts}</div>
                            <div className="text-sm text-gray-600">Tổng số bài</div>
                        </div>
                    </div>
                </div>

                <div className="card bg-gradient-to-br from-purple-50 to-pink-50">
                    <div className="flex items-center space-x-3">
                        <div className="p-3 bg-purple-500 rounded-lg">
                            <ChartBarIcon className="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <div className="text-3xl font-bold text-purple-600">{stats.avgScore}%</div>
                            <div className="text-sm text-gray-600">Điểm TB</div>
                        </div>
                    </div>
                </div>

                <div className="card bg-gradient-to-br from-green-50 to-emerald-50">
                    <div className="flex items-center space-x-3">
                        <div className="p-3 bg-success rounded-lg">
                            <TrophyIcon className="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <div className="text-3xl font-bold text-success">{stats.highestScore}%</div>
                            <div className="text-sm text-gray-600">Cao nhất</div>
                        </div>
                    </div>
                </div>

                <div className="card bg-gradient-to-br from-orange-50 to-yellow-50">
                    <div className="flex items-center space-x-3">
                        <div className="p-3 bg-orange-500 rounded-lg">
                            <ClockIcon className="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <div className="text-3xl font-bold text-orange-600">
                                {Math.floor(stats.totalTime / 60)}
                            </div>
                            <div className="text-sm text-gray-600">Phút ôn tập</div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Charts */}
            {history.length > 0 && (
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="card">
                        <h3 className="text-xl font-bold text-gray-900 mb-4">Biểu đồ điểm số</h3>
                        <div className="h-64">
                            <Line data={lineChartData} options={chartOptions} />
                        </div>
                    </div>

                    <div className="card">
                        <h3 className="text-xl font-bold text-gray-900 mb-4">Số câu đúng/sai</h3>
                        <div className="h-64">
                            <Bar data={barChartData} options={chartOptions} />
                        </div>
                    </div>
                </div>
            )}

            {/* History table */}
            <div className="card">
                <h3 className="text-xl font-bold text-gray-900 mb-4">Lịch sử làm bài</h3>

                {history.length === 0 ? (
                    <div className="text-center py-12">
                        <p className="text-gray-600 mb-4">Bạn chưa làm bài thi nào</p>
                        <Link to="/" className="btn btn-primary">
                            Bắt đầu làm bài
                        </Link>
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50 border-b">
                                <tr>
                                    <th className="px-4 py-3 text-left text-sm font-medium text-gray-700">Đề thi</th>
                                    <th className="px-4 py-3 text-center text-sm font-medium text-gray-700">Điểm</th>
                                    <th className="px-4 py-3 text-center text-sm font-medium text-gray-700">Đúng/Sai</th>
                                    <th className="px-4 py-3 text-center text-sm font-medium text-gray-700">Thời gian</th>
                                    <th className="px-4 py-3 text-center text-sm font-medium text-gray-700">Ngày thi</th>
                                    <th className="px-4 py-3 text-center text-sm font-medium text-gray-700">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200">
                                {history.map((attempt) => (
                                    <tr key={attempt.id} className="hover:bg-gray-50">
                                        <td className="px-4 py-3 text-sm text-gray-900">{attempt.exam_name}</td>
                                        <td className="px-4 py-3 text-center">
                                            <span className={`font-bold ${attempt.score >= 80 ? 'text-success' :
                                                attempt.score >= 50 ? 'text-warning' :
                                                    'text-error'
                                                }`}>
                                                {parseFloat(attempt.score).toFixed(1)}%
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-center text-sm text-gray-700">
                                            <span className="text-success">{attempt.correct_answers}</span>
                                            {' / '}
                                            <span className="text-error">{attempt.wrong_answers}</span>
                                        </td>
                                        <td className="px-4 py-3 text-center text-sm text-gray-700">
                                            {attempt.formatted_time}
                                        </td>
                                        <td className="px-4 py-3 text-center text-sm text-gray-600">
                                            {attempt.completed_at}
                                        </td>
                                        <td className="px-4 py-3 text-center">
                                            <Link
                                                to={`/result/${attempt.id}`}
                                                className="text-primary-600 hover:text-primary-700 text-sm font-medium"
                                            >
                                                Xem chi tiết
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </div>
    )
}

export default StatisticsPage
