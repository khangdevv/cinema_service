import apiClient from './apiClient'

export const statisticsService = {
    // Get overview statistics
    getOverview: async () => {
        const response = await apiClient.get('/statistics/overview')
        return response.data
    },

    // Get progress data
    getProgress: async () => {
        const response = await apiClient.get('/statistics/progress')
        return response.data
    },

    // Get leaderboard (public)
    getLeaderboard: async () => {
        const response = await apiClient.get('/leaderboard')
        return response.data
    }
}
