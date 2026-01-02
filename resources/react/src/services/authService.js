import apiClient from './apiClient'

export const authService = {
    // Login
    login: async (email, password) => {
        const response = await apiClient.post('/login', { email, password })
        return response.data
    },

    // Register
    register: async (name, email, password, password_confirmation) => {
        const response = await apiClient.post('/register', {
            full_name: name,  // Backend expects 'full_name' not 'name'
            email,
            password,
            password_confirmation
        })
        return response.data
    },

    // Logout
    logout: async () => {
        const response = await apiClient.post('/logout')
        return response.data
    },

    // Get current user
    me: async () => {
        const response = await apiClient.get('/me')
        return response.data
    }
}
