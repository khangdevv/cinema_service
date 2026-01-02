import { createContext, useContext, useState, useEffect } from 'react'
import { authService } from '../services/authService'

const AuthContext = createContext(null)

export const useAuth = () => {
    const context = useContext(AuthContext)
    if (!context) {
        throw new Error('useAuth must be used within AuthProvider')
    }
    return context
}

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null)
    const [loading, setLoading] = useState(true)
    const [token, setToken] = useState(localStorage.getItem('quiz_token'))

    // Check if user is logged in on mount
    useEffect(() => {
        const initAuth = async () => {
            const storedToken = localStorage.getItem('quiz_token')
            const storedUser = localStorage.getItem('quiz_user')

            if (storedToken && storedUser) {
                setToken(storedToken)
                setUser(JSON.parse(storedUser))
            }

            setLoading(false)
        }

        initAuth()
    }, [])

    const login = async (email, password) => {
        try {
            const response = await authService.login(email, password)

            if (response.success && response.data) {
                const { token, user } = response.data

                localStorage.setItem('quiz_token', token)
                localStorage.setItem('quiz_user', JSON.stringify(user))

                setToken(token)
                setUser(user)

                return { success: true }
            }

            return { success: false, message: response.message || 'Đăng nhập thất bại' }
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Đăng nhập thất bại'
            }
        }
    }

    const register = async (name, email, password, passwordConfirmation) => {
        try {
            const response = await authService.register(name, email, password, passwordConfirmation)

            if (response.success && response.data) {
                const { token, user } = response.data

                localStorage.setItem('quiz_token', token)
                localStorage.setItem('quiz_user', JSON.stringify(user))

                setToken(token)
                setUser(user)

                return { success: true }
            }

            return { success: false, message: response.message || 'Đăng ký thất bại' }
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Đăng ký thất bại'
            }
        }
    }

    const logout = async () => {
        try {
            await authService.logout()
        } catch (error) {
            console.error('Logout error:', error)
        } finally {
            localStorage.removeItem('quiz_token')
            localStorage.removeItem('quiz_user')
            setToken(null)
            setUser(null)
        }
    }

    const value = {
        user,
        token,
        loading,
        isAuthenticated: !!token,
        login,
        register,
        logout
    }

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    )
}
