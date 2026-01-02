import apiClient from './apiClient'

export const examService = {
    // Get all exams
    getExams: async () => {
        const response = await apiClient.get('/exams')
        return response.data
    },

    // Get exam by slug with questions
    getExam: async (slug) => {
        const response = await apiClient.get(`/exams/${slug}`)
        return response.data
    },

    // Submit exam answers
    submitExam: async (examSlug, answers, timeSpent) => {
        const response = await apiClient.post('/submit', {
            exam_slug: examSlug,
            answers,
            time_spent: timeSpent
        })
        return response.data
    },

    // Get user's exam history
    getHistory: async () => {
        const response = await apiClient.get('/history')
        return response.data
    },

    // Get attempt detail
    getAttemptDetail: async (id) => {
        const response = await apiClient.get(`/attempts/${id}`)
        return response.data
    }
}
