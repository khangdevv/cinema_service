import { Link, useNavigate, useLocation } from 'react-router-dom'
import { useAuth } from '../../context/AuthContext'
import {
    HomeIcon,
    BookOpenIcon,
    ChartBarIcon,
    ArrowRightOnRectangleIcon,
    UserCircleIcon,
    AcademicCapIcon
} from '@heroicons/react/24/outline'

const Header = () => {
    const { user, logout } = useAuth()
    const navigate = useNavigate()
    const location = useLocation()

    const handleLogout = async () => {
        await logout()
        navigate('/login')
    }

    const navItems = [
        { to: '/', label: 'Trang chủ', icon: HomeIcon },
        { to: '/theory', label: 'Lý thuyết', icon: BookOpenIcon },
        { to: '/statistics', label: 'Thống kê', icon: ChartBarIcon }
    ]

    const isActive = (path) => {
        if (path === '/') {
            return location.pathname === path
        }
        return location.pathname.startsWith(path)
    }

    return (
        <header className="glass sticky top-0 z-50 border-b border-white/20">
            <div className="container mx-auto px-4 py-4 max-w-7xl">
                <div className="flex items-center justify-between">
                    {/* Logo */}
                    <Link to="/" className="flex items-center space-x-2 group">
                        <div className="p-2 bg-gradient-to-br from-primary-500 to-purple-600 rounded-lg">
                            <AcademicCapIcon className="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold text-gradient">Quiz System</h1>
                            <p className="text-xs text-gray-600">Ôn tập Mã nguồn mở</p>
                        </div>
                    </Link>

                    {/* Navigation */}
                    <nav className="hidden md:flex items-center space-x-1">
                        {navItems.map((item) => {
                            const Icon = item.icon
                            const active = isActive(item.to)

                            return (
                                <Link
                                    key={item.to}
                                    to={item.to}
                                    className={`flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 ${active
                                            ? 'bg-primary-500 text-white shadow-lg'
                                            : 'text-gray-700 hover:bg-primary-50'
                                        }`}
                                >
                                    <Icon className="w-5 h-5" />
                                    <span className="font-medium">{item.label}</span>
                                </Link>
                            )
                        })}
                    </nav>

                    {/* User menu */}
                    <div className="flex items-center space-x-4">
                        <div className="hidden sm:flex items-center space-x-2 px-4 py-2 glass rounded-lg">
                            <UserCircleIcon className="w-5 h-5 text-primary-600" />
                            <span className="text-sm font-medium text-gray-700">
                                {user?.name || user?.email}
                            </span>
                        </div>

                        <button
                            onClick={handleLogout}
                            className="flex items-center space-x-2 px-4 py-2 rounded-lg bg-error/10 text-error hover:bg-error/20 transition-colors duration-200"
                        >
                            <ArrowRightOnRectangleIcon className="w-5 h-5" />
                            <span className="hidden sm:inline font-medium">Đăng xuất</span>
                        </button>
                    </div>
                </div>

                {/* Mobile nav */}
                <nav className="md:hidden flex justify-around mt-4 pt-4 border-t border-gray-200">
                    {navItems.map((item) => {
                        const Icon = item.icon
                        const active = isActive(item.to)

                        return (
                            <Link
                                key={item.to}
                                to={item.to}
                                className={`flex flex-col items-center space-y-1 px-3 py-2 rounded-lg transition-all ${active
                                        ? 'bg-primary-500 text-white'
                                        : 'text-gray-600 hover:bg-primary-50'
                                    }`}
                            >
                                <Icon className="w-5 h-5" />
                                <span className="text-xs font-medium">{item.label}</span>
                            </Link>
                        )
                    })}
                </nav>
            </div>
        </header>
    )
}

export default Header
