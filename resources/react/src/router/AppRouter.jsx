import { Routes, Route, Navigate } from 'react-router-dom'
import { ProtectedRoute } from './ProtectedRoute'

// Pages
import Login from '../pages/auth/Login'
import Register from '../pages/auth/Register'
import Dashboard from '../pages/dashboard/Dashboard'
import ExamPage from '../pages/exam/ExamPage'
import ResultPage from '../pages/result/ResultPage'
import TheoryPage from '../pages/theory/TheoryPage'
import StatisticsPage from '../pages/statistics/StatisticsPage'

// Layout
import MainLayout from '../components/layout/MainLayout'

const AppRouter = () => {
    return (
        <Routes>
            {/* Public routes */}
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />

            {/* Protected routes */}
            <Route
                path="/"
                element={
                    <ProtectedRoute>
                        <MainLayout />
                    </ProtectedRoute>
                }
            >
                <Route index element={<Dashboard />} />
                <Route path="exam/:slug" element={<ExamPage />} />
                <Route path="result/:attemptId" element={<ResultPage />} />
                <Route path="theory" element={<TheoryPage />} />
                <Route path="theory/:slug" element={<TheoryPage />} />
                <Route path="statistics" element={<StatisticsPage />} />
            </Route>

            {/* Catch all */}
            <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
    )
}

export default AppRouter
