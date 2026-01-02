import axios from 'axios'

const apiClient = axios.create({
    baseURL: '/api/quiz',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    withCredentials: true
})

// Request interceptor - add token
apiClient.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('quiz_token')
        if (token) {
            config.headers.Authorization = `Bearer ${token}`
        }
        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

// Response interceptor - handle errors
apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Unauthorized - clear token and redirect to login
            localStorage.removeItem('quiz_token')
            localStorage.removeItem('quiz_user')
            window.location.href = '/quiz/login'
        }
        return Promise.reject(error)
    }
)

export default apiClient
