import apiClient from './apiClient'

export const theoryService = {
    // Get all topics
    getTopics: async () => {
        const response = await apiClient.get('/topics')
        return response.data
    },

    // Get topic by slug
    getTopic: async (slug) => {
        const response = await apiClient.get(`/topics/${slug}`)
        return response.data
    },

    // Get all theories
    getAllTheories: async () => {
        const response = await apiClient.get('/theories')
        return response.data
    },

    // Get theory by slug
    getTheory: async (slug) => {
        const response = await apiClient.get(`/theories/${slug}`)
        return response.data
    }
}
