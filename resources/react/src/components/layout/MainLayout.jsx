import { Outlet } from 'react-router-dom'
import Header from './Header'

const MainLayout = () => {
    return (
        <div className="min-h-screen">
            <Header />
            <main className="container mx-auto px-4 py-8 max-w-7xl">
                <Outlet />
            </main>
        </div>
    )
}

export default MainLayout
